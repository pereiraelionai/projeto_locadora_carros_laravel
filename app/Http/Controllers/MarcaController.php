<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    public function __construct(Marca $marca) {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('attr_modelos')) {
            $attr_modelos = 'modelos:id,'.$request->attr_modelos;
            $marcaRepository->selectAttrRegistrosSelecionados($attr_modelos);
        } else {
            $marcaRepository->selectAttrRegistrosSelecionados('modelos');
        }

        if($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $marcaRepository->selectAttr($request->atributos);
        }
        
        //-------------------------------------------------------------------------
        /* Abaixo o código sem a utilização de design patterns
        $marcas = array();

        if($request->has('attr_modelos')) {
            $attr_modelos = $request->attr_modelos;
            $marcas = $this->marca->with('modelos:id,'.$attr_modelos);
        } else {
            $marcas = $this->marca->with('modelos');
        }

        if($request->has('filtro')) {
            $filtro = explode(';', $request->filtro);
            foreach($filtro as $key => $condicao) {
                $c = explode(':',$condicao);
                //dd(explode(':',$request->filtro));
                $marcas = $marcas->where($c[0], $c[1], $c[2]);
            }
        }

        if($request->has('atributos')) {
            $atributos = $request->atributos;
            //dd($attr_marca);
            //dd($request->atributo);
            $marcas = $marcas->selectRaw($atributos)->get();

        } else {
            $marcas = $marcas->get();
        }        

        //$marcas = Marca::all();
        //$marcas = $this->marca->with('modelos')->get(); //para utilizar o with dos relacionamentos sempre deve ser usado o método get e não o all
        */
        return response()->json($marcaRepository->getResultadoPaginado(5), 200);
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404) ;
        } 

        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($marca->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $marca->feedback());

        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }
        
        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $marca->fill($request->all());

        //verifica se a imagem foi enviada na requisição
        if($request->file('imagem')) {
            //remove o arquivo antigo
            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagem', 'public');
            $marca->imagem = $imagem_urn;
        }
        $marca->save();
        
        /*//serve apenas para o método put
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        //dd($request->nome);
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;
        //dd($marca->getAttributes());
        $marca->save();
        /*
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        */

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo
        Storage::disk('public')->delete($marca->imagem);        

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
        
    }
}

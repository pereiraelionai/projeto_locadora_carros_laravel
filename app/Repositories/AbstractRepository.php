<?php

namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;


abstract class AbstractRepository {
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAttrRegistrosSelecionados($atributos) {
        $this->model = $this->model->with($atributos);
    }

    public function filtro($filtro) {
        $filtro = explode(';', $filtro);
        foreach($filtro as $key => $condicao) {
            $c = explode(':',$condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }        
    }

    public function selectAttr($atributos='') {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultado() {
        return $this->model->get();
    }

    public function getResultadoPaginado($numPag) {
        return $this->model->paginate($numPag);
    }
}








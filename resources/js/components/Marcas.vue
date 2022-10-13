<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <!-- início do card de busca -->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:botao>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de busca -->


                <!-- início do card de listagem de marcas -->
                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                        <table-component 
                        :visualizar="{
                            visivel: true,
                            dataToggle: 'modal',
                            dataTarget: '#modalMarcaVisualizar'
                        }"
                        :atualizar="{
                            visivel: true,
                            dataToggle: 'modal',
                            dataTarget: '#modalMarcaAtualizar'
                        }"
                        :remover="{
                            visivel: true,
                            dataToggle: 'modal',
                            dataTarget: '#modalMarcaRemover'
                        }"
                        :dados="marcas.data" 
                        :titulos="{
                            id: {titulo: 'ID', tipo: 'text'},
                            nome: {titulo: 'Nome', tipo: 'text'},
                            imagem: {titulo: 'Imagem', tipo: 'imagem'},
                            created_at: {titulo: 'Data de criação', tipo: 'data'}
                        }"
                        ></table-component>
                    </template>

                    <template v-slot:botao>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key" :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                                        <a class="page-link" href="#" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>


        <!--Inicio do modal de inclusão de marca-->
        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component :detalhes="transacaoDetalhes" tipo="success" titulo="Adicionado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component :detalhes="transacaoDetalhes" tipo="danger" titulo="Erro ao salvar" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca.">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                </div>
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG.">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione a imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>                 
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>                
            </template>
        </modal-component>

        <!--Inicio do modal de visualização de marca-->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:alertas></template>
            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" :value="$store.state.item.id" class="form-control" disabled>
                </input-container-component>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" :value="$store.state.item.nome" class="form-control" disabled>
                </input-container-component>     
                <input-container-component titulo="Logo">
                    <br>
                    <img :src="'storage/'+$store.state.item.imagem" width="120" height="120" v-if="$store.state.item.imagem">
                </input-container-component>      
                <input-container-component titulo="Criado em:">
                    <input type="text" :value="$store.state.item.created_at|formataDataTempoGlobal" class="form-control" disabled>
                </input-container-component>                                     
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>

        <!--Inicio do modal de excluir marca-->
        <modal-component id="modalMarcaRemover" titulo="Remover">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Registro excluído" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'" ></alert-component>
                <alert-component tipo="danger" titulo="Erro" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <input-container-component titulo="ID">
                    <input type="text" :value="$store.state.item.id" class="form-control" disabled>
                </input-container-component>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" :value="$store.state.item.nome" class="form-control" disabled>
                </input-container-component>     
                <input-container-component titulo="Logo">
                    <br>
                    <img :src="'storage/'+$store.state.item.imagem" width="120" height="120" v-if="$store.state.item.imagem">
                </input-container-component>      
                <input-container-component titulo="Criado em:">
                    <input type="text" :value="$store.state.item.created_at|formataDataTempoGlobal" class="form-control" disabled>
                </input-container-component>                                     
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>     
        
        <!--Inicio do modal de editar marca-->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Registro Atualizado" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'" ></alert-component>
                <alert-component tipo="danger" titulo="Erro" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>                
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="atualizarNome" id-help="atualizarNomeHelp" texto-ajuda="Informe o nome da marca.">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da marca" v-model="$store.state.item.nome">
                    </input-container-component>
                </div>
                <div class="form-group">
                    <input-container-component titulo="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG.">
                        <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Selecione a imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>                 
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" @click="atualizar()" >Atualizar</button>
            </template>
        </modal-component>        


    </div>
</template>

<script>
import axios from 'axios'

    export default {
        /* Enviado para o interceptador em bootstrap.js
        computed: {
                token() {
                    let token = document.cookie.split(';').find(indice => {
                        return indice.includes('token=')
                    })
                    token = token.split('=')[1]
                    token = 'Bearer ' + token
                    return token
                }
            }, 
            */       
        data() {
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: {},
                marcas: {data: []},
                busca: {id: '', nome: ''}
            }
        },
        methods: {
            atualizar() {
                //console.log(this.$store.state.item)
                let url = this.urlBase + '/' + this.$store.state.item.id

                let formData = new FormData();
                formData.append('_method', 'patch')
                formData.append('nome', this.$store.state.item.nome)
                if(this.arquivoImagem[0]) {
                    formData.append('imagem', this.arquivoImagem[0])
                }
                let config = {
                    headers: {
                        'Content-Type' : 'multipart/form-data',
                        /*enviando ps headers abaixo no interceptador das requisições
                        'Accept': 'application/json',
                        'Authorization': this.token
                        */
                    }
                }

                axios.post(url, formData, config)
                    .then(response => {
                        //console.log('Atualizado', response)
                        atualizarImagem.value = ''
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso'                        
                        this.carregarLista()
                    })
                    .catch(errors => {
                        //console.log('Erro de atualziação', errors.response)
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.message                        
                        this.$store.state.transacao.dados = errors.response.data.errors
                    })
            },
            remover() {
                let confirmacao = confirm('Tem certeza que deseja remover o registro?')
                let url = this.urlBase + '/' + this.$store.state.item.id
                let formData = new FormData();
                formData.append('_method', 'delete')
                /* enviando no interceptador
                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
                */


                if(!confirmacao) {
                    return false
                }

                console.log(this.$store.state.transacao)
                
                axios.post(url, formData)
                    .then(response => {
                        //console.log('Registro removido com sucesso', response)
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.mensagem = response.data.msg
                        this.carregarLista()
                    })
                    .catch(errors => {
                        //console.log('Houve um erro na tentativa de remoção', errors.response)
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.mensagem = errors.response.data.message
                    })
            },
            pesquisar() {
                //console.log(this.busca)

                let filtro = ''
                
                for(let chave in this.busca) {
                    //console.log(chave, this.busca[chave])
                    if(this.busca[chave]) {
                        if(filtro != '') {
                        filtro += ';'
                    }
                    filtro += chave + ':like:' + this.busca[chave]
                }                        
            }
                //console.log(filtro)
                if(filtro != '') {
                    this.urlPaginacao = 'page=1'
                    this.urlFiltro = '&filtro='+filtro
                } else {
                    this.urlFiltro = ''
                }
                this.carregarLista()
            },
            paginacao(l) {
                //console.log(l)
                if(l.url) {
                    //this.urlBase = l.url
                    this.urlPaginacao = l.url.split('?')[1]
                    this.carregarLista()
                }
            },  
            carregarLista() {

                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
                /*
                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
                */

                axios.get(url)
                    .then(response => {
                        this.marcas = response.data
                        //console.log(this.marcas)
                    })
                    .catch(errors => {
                        console.log(errors)
                    })
            },
            carregarImagem(event) {
                this.arquivoImagem = event.target.files
            },
            salvar() {
                let formData = new FormData();
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])

                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }

                axios.post(this.urlBase, formData, config)
                    .then(response => {
                        this.transacaoStatus = 'adicionado'
                        this.transacaoDetalhes = {
                            mensagem: 'ID do registro '+ response.data.id
                        }
                        this.carregarLista()
                        console.log(response)
                    })
                    .catch(errors => {
                        this.transacaoStatus = 'erro'
                        this.transacaoDetalhes = {
                            mensagem: errors.response.data.message,
                            dados: errors.response.data.errors
                        }
                        console.log(errors.response.data.message)
                    })
            }
        },
        mounted() {
            this.carregarLista()
        }
    }
</script>

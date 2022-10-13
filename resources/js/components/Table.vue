<template>
<div>
    <table class="table table-hover">
        <thead>
            <tr>
                <!-- antes de fazer com obj literal
                <th class="text-uppercase" scope="col" v-for="t, key in titulos" :key="key">{{t}}</th>
                -->
                <th scope="col" v-for="t, key in titulos" :key="key">{{t.titulo}}</th>
                <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel"></th>
            </tr>
        </thead>
        <tbody>

            <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                <td v-for="valor, chaveValor in obj" :key="chaveValor">
                    <span v-if="titulos[chaveValor].tipo == 'text'">
                        {{valor}}
                    </span>
                    <span v-if="titulos[chaveValor].tipo == 'imagem'">
                        <img :src="'/storage/'+valor" width="30" height="30">
                    </span>
                    <span v-if="titulos[chaveValor].tipo == 'data'">
                        {{valor|formataDataTempoGlobal}}
                    </span>                  
                </td>
                <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
                    <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget" @click="setStore(obj)">Visualizar</button>
                    <button v-if='atualizar.visivel' class="btn btn-outline-dark btn-sm" :data-toggle="atualizar.dataToggle" :data-target="atualizar.dataTarget" @click="setStore(obj)">Atualizar</button>
                    <button v-if="remover.visivel" class="btn btn-outline-danger btn-sm" :data-toggle="remover.dataToggle" :data-target="remover.dataTarget" @click="setStore(obj)">Remover</button>
                </td>
            </tr>

            <!-- Antes de utilizar obj literal
            <tr v-for="obj in dados" :key="obj.id">
                <td v-if="titulos.includes(chave)" v-for="valor, chave in obj" :key="chave">
                    <span v-if="chave == 'imagem'">
                        <img :src="'/storage/'+valor" width="30" height="30">
                    </span>
                    <span v-else>
                        {{valor}}
                    </span>
                </td>
            </tr>
            -->

            <!--
            <tr v-for="m in dados" :key="m.id">
                <th scope="row">{{m.id}}</th>
                <td>{{m.nome}}</td>
                <td> <img :src="'/storage/'+m.imagem" width="30" height="30"></td>
            </tr>
            -->
        </tbody>
    </table>
</div>
</template>

<script>
    export default {
        props: ['dados', 'titulos', 'atualizar', 'visualizar', 'remover'],
        computed: {
            dadosFiltrados() {
                let campos = Object.keys(this.titulos)
                let dadosFiltrados = []
                //console.log(this.titulos)
                //console.log(this.dados)
                this.dados.map((item, chave) => {
                    //console.log(chave, item)

                    let itemFiltrado = {}
                    campos.forEach(campo => {
                        itemFiltrado[campo] = item[campo]
                        //console.log(campo, item, campo)
                    })
                    //console.log(itemFiltrado)
                    dadosFiltrados.push(itemFiltrado)
                })
                //console.log(dadosFiltrados)
                return dadosFiltrados
            }
        },
        methods: {
            setStore(obj) {
                //console.log(obj)
                this.$store.state.transacao.status = ''
                this.$store.state.transacao.mensagem = ''
                this.$store.state.transacao.dados = ''
                this.$store.state.item = obj
            }
        }
    }
</script>
<template>
    <div class="card mb-4">
        <div class="card-header">{{titulo}}</div>
            <div class="card-body">
                <slot name="conteudo"></slot>
                <slot name="botao"></slot>
            </div>
    </div>
</template>

<script>
    export default {
        props: ['titulo']
    }
</script>
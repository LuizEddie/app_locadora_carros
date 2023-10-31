<template>
    <div :class="estilo" role="alert">
        A mensagem de feedback
    </div>
</template>

<script>
export default {
    props: ['tipo'],
    computed: {
        estilo(){
            return this.tipo === "success" ? "alert alert-success" : "alert alert-danger";
        }
    }
};
</script>

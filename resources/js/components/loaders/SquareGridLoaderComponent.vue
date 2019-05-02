<template>
    <div v-bind:class="'cmn-loader-container cmn-cube-grid' + color + (loading ? ' loading': '')">
        <div v-for="index in 9" v-bind:class="'cmn-loader-cube cmn-loader-cube' + index"></div>
    </div>
</template>

<script>
    export default {
        name: "SquareGridLoaderComponent",
        props: {
            loading: {
                type: Boolean,
                default: false,
            },
            color: {
                type: String,
                default: ' c2',
            }
        },
        data: function() {
            return {
            }
        },
    }
</script>

<style scoped>

</style>

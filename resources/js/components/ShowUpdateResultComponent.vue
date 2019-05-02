<template>
    <div class="col-12">
        <div class="row no-gutters mb-3">
            <div class="col-2">
                {{airing_at}}
            </div>
            <div class="col-5">
                <a v-bind:href="'/episode/' + uuid" class="text-dark">{{show_name}}</a>
            </div>
            <div class="col-5">
                {{episode_code}}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowUpdateResultComponent",
        props: ['show_name', 'uuid', 'api_link', 'airing_at', 'episode_code', 'summary'],
        data: function() {
            return {}
        },
    }
</script>

<style scoped>

</style>

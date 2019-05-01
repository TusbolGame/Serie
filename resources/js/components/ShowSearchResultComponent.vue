<template>
    <div class="col-12">
        <div class="row no-gutters mb-3">
            <div class="col-3">
                <img class="w-100" v-bind:src="poster" >
            </div>
            <div class="col-9">
                <h5 class="col-12">
                    <a v-bind:href="api_link" class="text-dark">{{name}}</a>
                    <span v-if="api_rating != null"> - {{api_rating}}</span>
                </h5>
                <div class="col-12 show-description">
                    {{description}}
                </div>
                <div class="col-12 pt-3 d-flex justify-content-end">
                    <button v-on:click="addShow(api_id)" type="button" class="btn btn-md btn-link" data-group="0" data-type="2">Add</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowSearchResultComponent",
        props: ['name', 'api_id', 'api_id', 'api_link', 'api_rating', 'description', 'poster'],
        methods: {
            addShow: function(api_id) {
                this.$emit('add-show', api_id);
            }
        }
    }
</script>

<style scoped>

</style>

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
            </div>
            <div class="col-12 pt-3">
                <div class="row no-gutters text-center">
                    <span v-if="updated && !added" class="col-12">The show was added to the database.</span>
                    <span v-if="updated && added" class="col-12">The show was added to your shows.</span>
                    <span v-if="errorType.addToDB" class="col-12">The show is already present in the Database.</span>
                    <span v-if="errorType.addToUser" class="col-12">The show was NOT added to your shows.</span>
                </div>
                <div class="row no-gutters d-flex justify-content-end">
                    <button v-if="!error && !updated && !added" v-on:click="addShow(api_id)" type="button" class="btn btn-md btn-link" data-group="0" data-type="2">Add</button>
                    <button v-if="!error && updated && !added" v-on:click="addUserShow(uuid)" type="button" class="btn btn-md btn-primary" data-group="0" data-type="3">Add to your shows</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowSearchResultComponent",
        props: ['name', 'api_id', 'api_id', 'api_link', 'api_rating', 'description', 'poster'],
        data: function() {
                return {
                    updated: false,
                    added: false,
                    uuid: '',
                    errorType: {
                        addToDB: false,
                        addToUser: false,
                    },
                    error: false
                }
            },
        methods: {
            addShow: function(api_id) {
                window.axios.get('/data/update/3/' + api_id)
                    .then(({data}) => {
                        this.uuid = data.data;
                        this.updated = true;
                    }).catch((error) => {
                    console.log(error.response.data);
                    this.error = true;
                    this.errorType.addToDB = true;
                });
            },
            addUserShow: function (uuid) {
                window.axios.get('/show/add/' + uuid)
                    .then(({data}) => {
                        this.added = true;
                    }).catch((error) => {
                    console.log(error.response.data);
                    this.error = true;
                    this.errorType.addToUser = true;
                });
            }
        }
    }
</script>

<style scoped>

</style>

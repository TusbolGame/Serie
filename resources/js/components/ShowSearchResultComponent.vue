<template>
    <div class="col-12">
        <div class="row no-gutters mb-3">
            <div class="col-3">
                <img class="w-100" v-bind:src="poster" >
            </div>
            <div class="col-9">
                <div class="col-12 show-description">
                    {{description}}
                </div>
            </div>
            <div class="col-12 pt-3">
                <div class="row no-gutters text-center">
                    <span v-if="updated && !added" class="col-12">The show was added to the database.</span>
                    <span v-if="adding" class="col-12">The show is being added to the database.</span>
                    <span v-if="updated && added" class="col-12">The show was added to your shows.</span>
                    <span v-if="owned" class="col-12">The show is already in your shows.</span>
                    <span v-if="(existing && !owned) || errorType.addToDB" class="col-12">The show is already present in the Database.</span>
                    <span v-if="errorType.addToUser" class="col-12">The show was NOT added to your shows.</span>
                </div>
                <div class="row no-gutters d-flex justify-content-end mt-2">
                    <button v-if="!existing && !adding && !error && !updated && !added" v-on:click="addShow(api_id)" type="button" class="btn btn-md btn-link" data-group="0" data-type="2">Add</button>
                    <button v-if="(existing || updated) && !owned && !error && !added" v-on:click="addUserShow(uuid)" type="button" class="btn btn-md btn-primary" data-group="0" data-type="3">Add to your shows</button>
                </div>
            </div>
            <div class="cover pb-3">

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowSearchResultComponent",
        props: ['show_name', 'api_id', 'api_id', 'api_link', 'api_rating', 'description', 'poster', 'existing', 'owned'],
        data: function() {
                return {
                    updated: false,
                    added: false,
                    adding: false,
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
                this.adding = true;
                window.axios.get('/data/update/3/' + api_id)
                    .then(({data}) => {
                        props.existing = true;
                        this.adding = false;
                        this.uuid = data.data;
                        this.updated = true;
                    }).catch((error) => {
                    console.log(error.response.data);
                    this.adding = false;
                    this.error = true;
                    this.errorType.addToDB = true;
                });
            },
            addUserShow: function (uuid) {
                window.axios.get('/show/add/' + uuid)
                    .then(({data}) => {
                        this.added = true;
                        props.owned = true;
                    }).catch((error) => {
                    console.log(error.response.data);
                    this.error = true;
                    this.errorType.addToUser = true;
                });
            }
        },
        watch: {
            muteShow(val) {
                document.getElementsByClassName('cover').className = val ? "cover active" : "cover";
            }
        }
    }
</script>

<style scoped>
    .cover {
        position: absolute;
    }

    .cover.active {
        opacity: 0.8;
        z-index: 99;
        background: #FFFFFF;
        height: 100%;
        width: 100%;
    }
</style>

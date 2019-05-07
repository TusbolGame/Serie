<template>
    <div class="col-12">
        <div class="result row no-gutters mb-3">
            <div class="col-3">
                <img class="w-100" v-bind:src="poster" >
            </div>
            <div class="col-9">
                <div class="col-12 show-description">{{description}}</div>
            </div>
            <div class="col-12 pt-3">
                <div class="row no-gutters text-center">
                    <span v-if="updated && !added" class="col-12">The show was added to the database.</span>
                    <span v-if="adding" class="col-12">The show is being added to the database.</span>
                    <span v-if="updated && added" class="col-12">The show was added to your shows.</span>
                    <span v-if="owned" class="col-12">The show is in your shows.</span>
                    <span v-if="(existing && !owned) || errorType.addToDB" class="col-12">The show is in the Database.</span>
                    <span v-if="errorType.addToUser" class="col-12">The show was NOT added to your shows.</span>
                </div>
                <div class="row no-gutters d-flex justify-content-end mt-2">
                    <button v-if="!existing && !adding && !error && !updated && !added" v-on:click="addShow(api_id)" type="button" class="btn btn-md btn-link" data-group="0" data-type="2">Add</button>
                    <button v-if="(existing || updated) && !owned && !error && !added" v-on:click="addUserShow(uuid)" type="button" class="btn btn-md btn-primary" data-group="0" data-type="3">Add to your shows</button>
                </div>
            </div>
            <div v-show="adding" v-bind:class="{'cover pb-3 d-flex justify-content-center align-items-center':true, 'active':(adding)}">
                <div class="curtain"></div>
                <square-grid-loader-component v-bind:loading="adding"></square-grid-loader-component>
            </div>
        </div>
    </div>
</template>

<script>
    import SquareGridLoaderComponent from './loaders/SquareGridLoaderComponent.vue'

    export default {
        name: "ShowSearchResultComponent",
        components: {
            SquareGridLoaderComponent
        },
        props: {
            show_name: {
                type: String,
            },
            api_id: {
                type: Number,
            },
            api_link: {
                type: String,
            },
            api_rating: {
                type: Number ,
            },
            description: {
                type: String,
            },
            poster: {
                type: String,
            },
            existing: {
                type: Boolean,
            },
            owned: {
                type: Boolean,
            },
        },
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
                    console.log(error.response);
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
                    console.log(error.response);
                    this.error = true;
                    this.errorType.addToUser = true;
                });
            }
        },
        watch: {
        }
    }
</script>

<style scoped>
    .cover {
        position: absolute;
    }

    .cover .cmn-loader-container {
        display: none;
    }

    .cover.active {
        z-index: 99;
        height: 100%;
        width: 100%;
    }

    .cover.active .curtain {
        position: absolute;
        opacity: 0.8;
        background: #FFFFFF;
        height: 100%;
        width: 100%;
    }

    .cover.active .cmn-loader-container {
        display: block;
    }
</style>

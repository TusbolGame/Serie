<template>
    <div class="col-12">
        <div class="row no-gutters mb-1 d-flex justify-content-between">
            <div class="col-10 d-flex align-items-center">
                <div v-if="!data.fetched" class="row no-gutters w-100 d-flex justify-content-between">
                    Fetching download data...
                </div>
                <div v-if="data.fetched" class="row no-gutters w-100 d-flex justify-content-between">
                    <div class="col-9 d-inline-block text-truncate">{{data.show}}</div>
                    <div>{{data.episode_code}}</div>
                </div>
            </div>
            <div v-if="data.fetched">
                <button v-if="!details.visible" @click="toggleDetails" type="button" class="btn btn-md btn-link pr-0" data-group="0" data-type="7">Details</button>
                <button v-if="details.visible" @click="toggleDetails" type="button" class="btn btn-md btn-link pr-0" data-group="0" data-type="8">Hide</button>
            </div>
            <div v-if="details.visible" class="col-12 my-2 pl-4">
                <div class="row no-gutters d-flex justify-content-between">
                    <div class="col-7 d-inline-block text-truncate text-black-50">{{data.name}}</div>
                    <div class="col-3" v-if="progress.visible">
                        <progress-bar-component v-bind:width="progress.percentage">
                        </progress-bar-component>
                    </div>
                    <div class="text-black-50 font-weight-light">{{progress.percentage}}%</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ProgressBarComponent from './ProgressBarComponent.vue'
    export default {
        name: "ShowDownloadResultComponent",
        components: {
            ProgressBarComponent
        },
        props: {
            fileName: {
                type: String,
                default: '',
            },
            show: {
                uuid: {
                    type: String,
                    default: '',
                },
                name: {
                    type: String,
                    default: '',
                },
            },
            episode: {
                uuid: {
                    type: String,
                    default: '',
                },
                episode_code: {
                    type: String,
                    default: '',
                },
            },
            infoHash: {
                type: String,
                default: '',
            },
        },
        data: function() {
            return {
                data: {
                    fetched: false,
                },
                details: {
                    visible: false,
                },
                progress: {
                    visible: false,
                    percentage: 0,
                },
            }
        },
        methods: {
            toggleDetails: function() {
                this.details.visible = !this.details.visible;
            }
        },
    }
</script>

<style scoped>

</style>

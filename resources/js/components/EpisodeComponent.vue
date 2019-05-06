<template>
    <div v-bind:class="'card-episode col-xl-2 col-lg-3 col-md-4 col-sm-12 px-1 mb-2' + (cardExpanded ? ' expanded' : '')" v-bind:data-airdate="airing_at"
         v-bind:data-episode="uuid" v-bind:data-filter="show_name"
         v-bind:data-show="show_uuid">
    <div class="card d-flex">
            <div class="episode-poster-container card-img-top">
                <img class="episode-poster" v-bind:src="'/img/posters/' + show_posters[0].name + '.jpg'" alt="">
            </div>
            <div class="card-body d-flex flex-column justify-content-between no-gutters p-1 bg-light">
                <div class="card-info">
                    <div class="card-info-default">
                        <div class="row no-gutters card-info-default-container">
                            <div class="col-12 episode-show-name">
                                <a v-bind:href="'/show/' + show_uuid" class="card-title h4 text-dark">{{show_name}}</a>
                            </div>
                            <div class="col-12 episode-code">
                                <a v-bind:href="'/episode/' + uuid" class="card-title h4 font-weight-light cmn-light">{{episode_code}}</a>
                            </div>
                        </div>
                        <div class="episode-date font-weight-light cmn-lighter" v-bind:data-date="airing_at">
                            {{displayDateFromNow(airing_at)}}
                        </div>
                    </div>
                    <div v-if="summary !== null" class="card-info-extra pt-2 font-weight-light">
                        {{summary | truncate(100)}}
                    </div>
                </div>
                <div class="card-actions">
                    <div class="actions-extra d-flex justify-content-between align-items-end">
                        <icon-button-component v-bind:button-class="'c12'"
                                               v-bind:type="7"
                                               v-bind:group="1"
                                               v-bind:title="'Remove show'"
                                               v-bind:icon="'default-clear'"
                                               v-on:click="removeUserShow"></icon-button-component>
                        <icon-button-component v-bind:button-class="'c3'"
                                               v-bind:type="8"
                                               v-bind:group="1"
                                               v-bind:title="'Add bookmark'"
                                               v-bind:icon="'default-bookmark'"></icon-button-component>
                        <icon-button-component v-bind:button-class="'c1'"
                                               v-bind:type="9"
                                               v-bind:group="1"
                                               v-bind:title="'Show more details'"
                                               v-bind:icon="'default-list'"></icon-button-component>
                        <icon-button-component v-bind:button-class="'c5'"
                                               v-bind:type="10"
                                               v-bind:group="1"
                                               v-bind:title="'Rate this episode'"
                                               v-bind:icon="'default-star'"></icon-button-component>
                    </div>
                    <div class="actions-basic">
                        <template  v-if="torrent_count == 0 || torrent[0].status == 5">
                            <a v-bind:href="torrentHREF" target="_blank">
                                <icon-button-component v-bind:button-class="'c6'"
                                                       v-bind:type="1"
                                                       v-bind:group="1"
                                                       v-bind:title="'Search for torrents'"
                                                       v-bind:icon="'default-search'"></icon-button-component>
                            </a>
                                <icon-button-component v-bind:button-class="'c9'"
                                                       v-bind:type="2"
                                                       v-bind:group="1"
                                                       v-bind:title="'Add magnet link'"
                                                       v-bind:icon="'default-torrent'"
                                                       v-on:click="addMagnetLink"></icon-button-component>
                        </template>
                        <template v-else>
                            <template v-if="torrent[0].status == 0 || torrent[0].status == 1">
                                <icon-button-component v-bind:button-class="'c9'"
                                                       v-bind:type="2"
                                                       v-bind:group="1"
                                                       v-bind:title="'Add magnet link'"
                                                       v-bind:icon="'default-torrent'"
                                                       v-on:click="addMagnetLink"></icon-button-component>
                                <icon-button-component v-bind:button-class="'c11'"
                                                       v-bind:type="6"
                                                       v-bind:group="1"
                                                       v-bind:title="'Check torrent status'"
                                                       v-bind:icon="'default-torrent'"></icon-button-component>
                            </template>
                            <template v-else-if="torrent[0].status == 2">
                                <icon-button-component v-bind:button-class="'c1'"
                                                       v-bind:type="3"
                                                       v-bind:group="1"
                                                       v-bind:title="'Convert Video'"
                                                       v-bind:icon="'default-convert'"></icon-button-component>
                            </template>
                            <template v-else-if="torrent[0].status == 3">
                                <icon-button-component v-bind:button-class="'c2'"
                                                       v-bind:type="4"
                                                       v-bind:group="1"
                                                       v-bind:title="'Play episode'"
                                                       v-bind:icon="'default-play'"></icon-button-component>
                            </template>
                        </template>
                        <icon-button-component v-bind:button-class="'c4'"
                                               v-bind:type="5"
                                               v-bind:group="1"
                                               v-bind:title="'Mark this episode as watched'"
                                               v-bind:icon="'default-mark'"
                                               v-on:click="episodeMarkWatched(true)"></icon-button-component>
                        <icon-button-component v-bind:button-class="'neu'"
                                               v-bind:type="0"
                                               v-bind:group="1"
                                               v-bind:title="'Show more information'"
                                               v-bind:icon="'default-expand-up'"
                                               v-on:click="!cardExpanded"></icon-button-component>
                    </div>
                </div>
            </div>
            <div v-show="cover.active" class="card-cover">
                <button v-show="cover.closerActive" class="card-cover-closer" @click="closeCover">
                </button>
                <div v-show="cover.loading" class="card-cover-loader d-flex justify-content-center align-items-center">
                    <square-grid-loader-component v-bind:loading="cover.loading"></square-grid-loader-component>
                </div>
                <div v-show="cover.splashing" v-bind:class="'card-cover-splash d-flex justify-content-center align-items-center' + cover.splashColor">
                    <!-- TODO Test transition -->
                    <transition name="fade" tag="div" appear>
                        <div v-if="cover.types.markAsWatched" key="markAsWatched" class="card-cover-item default-mark"></div>
                        <div v-if="cover.types.renewing" key="renewing" class="card-cover-item default-autorenew"></div>
                        <div v-if="cover.types.deleting" key="deleting" class="card-cover-item default-delete"></div>
                    </transition>
                </div>
                <div v-show="cover.error.state" class="card-cover-error d-flex flex-column justify-content-center align-items-center neg">
                    <div class="card-cover-item default-error"></div>
                    <div class="card-cover-error-message p-3">{{cover.error.message}}</div>
                </div>
            </div>
        </div>
    </div>
</template>
<!--<transition name="modal">-->
    <!--<div class="modal-mask">-->
        <!--<div class="modal-wrapper">-->
            <!--<div class="modal-container">-->

                <!--<div class="modal-header">-->
                    <!--<slot name="header">-->
                        <!--default header-->
                    <!--</slot>-->
                <!--</div>-->

                <!--<div class="modal-body">-->
                    <!--<slot name="body">-->
                        <!--default body-->
                    <!--</slot>-->
                <!--</div>-->

                <!--<div class="modal-footer">-->
                    <!--<slot name="footer">-->
                        <!--default footer-->
                        <!--<button class="modal-default-button" @click="$emit('close')">-->
                            <!--OK-->
                        <!--</button>-->
                    <!--</slot>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
    <!--</div>-->
<!--</transition>-->
<script>
    import IconButtonComponent from "./buttons/IconButtonComponent";
    import SquareGridLoaderComponent from './loaders/SquareGridLoaderComponent.vue'
    import VueEventBus from './VueEventBus';

    export default {
        name: "EpisodeComponent",
        data: function() {
            return {
                cardExpanded: false,
                cover: {
                    active: false,
                    closerActive: false,
                    loading: false,
                    splashing: false,
                    types: {
                        markAsWatched: false,
                        renewing: false,
                        deleting: false,
                    },
                    splashColor: '',
                    error: {
                        state: false,
                        message: '',
                    }
                },
            }
        },
        props: {
            uuid: {
                type: String,
            },
            show_name: {
                type: String
            },
            show_uuid: {
                type: String,
            },
            show_posters: {
                type: Array,
            },
            episode_code: {
                type: String,
            },
            airing_at: {
                type: String,
            },
            summary: {
                type: String,
            },
            torrent_count: {
                type: Number,
            },
            torrent: {
                type: Array,
            }
        },
        computed: {
            torrentHREF: function() {
                return 'https://rarbgway.org/torrents.php?search=' + this.show_name + ' ' + this.episode_code + '&category[]=18&category[]=41&category[]=49';
            },
        },
        components: {
            IconButtonComponent,
            SquareGridLoaderComponent,
        },
        methods: {
            displayDate: function(date, format) {
                return moment(date).format(format);
            },
            displayDateFromNow: function(date) {
                return moment(date).fromNow();
            },
            episodeMarkWatched: function(seen) {
                if (seen !== true && seen !== false) {
                    return false;
                }

                this.active = true;
                this.cover.loading = true;

                window.axios.get('/episode/view/mark/' + this.uuid + '/' + seen ? '1' : '0')
                    .then(({data}) => {
                        this.cover.loading = false;
                        this.cover.splashing = true;
                        this.cover.types.markAsWatched = true;
                        if (data.data.length !== 0) {
                            this.cover.types.renewing = true;
                            this.episode = data.data;
                        }
                    }).catch((error) => {
                    this.cover.loading = false;
                    this.cover.splashing = false;
                    this.error.state = true;
                    this.error.message = 'Mark as watched unsuccessful. See the console for more details.';

                    console.log(error.response);
                });
            },
            removeUserShow: function() {
                this.cover.loading = true;
                window.axios.get('/show/remove/' + this.show_uuid)
                    .then(({data}) => {
                        this.cover.loading = false;
                        this.cover.splashing = true;
                        this.cover.types.deleting = true;
                    }).catch((error) => {
                    this.cover.loading = false;
                    this.cover.splashing = false;
                    this.error.state = true;
                    this.error.message = 'Show removal unsuccessful. See the console for more details.';

                    console.log(error.response);
                });
            },
            addMagnetLink: function(magnetLink) {
                bootbox.prompt({
                    title: "Insert the magnet link",
                    backdrop: true,
                    callback:  function(magnetlink) {
                        if (magnetLink === null || magnetLink.trim().length === 0) {
                            console.log('The string provided is empty.');
                            return null;
                        }

                        var url = 'http://localhost:5000/torrent/add/' + episode_id + '/' + encodeURIComponent(magnetLink);
                        window.axios.get(url)
                            .then(({data}) => {
                            }).catch((error) => {
                            console.log(error.response);
                        });
                    }
                });
            },
            closeCover: function() {
                this.cover.active = false;
                this.cover.closerActive = false;
                this.cover.loading = false;
                this.cover.splashing = false;
                this.error.state = false;
            }
        },
        mounted () {
            VueEventBus.$on('buttonClassToCoverSplash', (buttonColor) => {
                this.cover.splashColor = ' ' . buttonClass;
            });
        },
    }
</script>

<style scoped>

</style>

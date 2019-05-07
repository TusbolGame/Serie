
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./Echo');

import Vue from 'vue'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./components', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


Vue.filter('truncate', function (text, stop, suffix) {
    return text.slice(0, stop) + (stop < text.length ? suffix || '...' : '');
});
// Common components
Vue.component('progress-bar-component', require('./components/ProgressBarComponent.vue').default);

// Specific components
Vue.component('show-search-result-component', require('./components/ShowSearchResultComponent.vue').default);
Vue.component('episode-component', require('./components/EpisodeComponent.vue').default);

var Episode = new Vue({
    el: '#UnwatchedEpisodes',
    data: {
        test: [],
    },
    methods: {
        // Transition methods
        beforeEnter: function (el) {
            el.style.opacity = 0;
        },
        enter: function (el, done) {
            var delay = el.dataset.index * 1500;
            setTimeout(function() {
                $(el).animate({ opacity: 1 }, 3000, done);
            }, delay)
        },
        leave: function (el, done) {
            var delay = el.dataset.index * 1500;
            setTimeout(function() {
                $(el).animate({ opacity: 0 }, 3000, done);
            }, delay)
        },
    },
});

var ShowSearch = new Vue({
    el: '#ShowSearch',
    data: {
        showSearchQuery: '',
        searchResults: [],
        active: false,
        noResults: false,
        searching: false,
        searched: false,
    },
    methods: {
        searchShow: function () {
            this.searchResults = [];
            this.searched = false;
            this.searching = false;
            this.active = false;
            if (this.showSearchQuery.trim().length > 0) {
                this.searching = true;
                window.axios.get('/data/search/show/' + this.showSearchQuery)
                    .then(({data}) => {
                        this.searched = true;
                        if (data.data.length > 0) {
                            this.active = true;
                        } else {
                            this.active = false;
                            this.noResults = true;
                        }
                        this.searching = false;
                        this.searchResults = data.data;
                    }).catch((error) => {
                    console.log(error.response);
                });
            } else {
            }
        },
        resetSearch: function() {
            this.searchResults = [];
            this.searched = false;
            this.searching = false;
            this.active = false;
            this.showSearchQuery = '';
        }
    }
});

Vue.component('show-update-result-component', require('./components/ShowUpdateResultComponent.vue').default);
var ShowUpdate = new Vue({
    el: '#ShowUpdate',
    data: {
        active: false,
        updating: false,
        updated: false,
        completed: false,
        updateResults: [],
        updateProgress: {
            counter: 0,
            current: 0,
            total: 0,
            percentage: 0,
            currentShow: '',
            timeStarted: 0,
            timeRemaining: 0,
        }
    },
    methods: {
        updateAllShows: function () {
            this.active = true;
            this.updateProgress.timeStarted = window.performance.now();
            this.updating = true;
            window.axios.get('/data/update/0')
                .then(({data}) => {
                    this.completed = true;
                    this.updating = false;
                }).catch((error) => {
                console.log(error.response);
            });
        },
        listenEpisodeUpdated: function() {
            window.Echo.private('data-update.' + window.Laravel.user)
                .listen('EpisodeCreated', (data) => {
                    this.updateResults.push(data.episode);
                    this.updated = true;
                    this.updateProgress.counter++;
                });
        },
        listenShowUpdated: function() {
            window.Echo.private('data-update.' + window.Laravel.user)
                .listen('ShowUpdated', (data) => {
                    this.updateProgress.current = data.currentShowNumber;
                    this.updateProgress.total = data.totalShowNumber;
                    this.updateProgress.percentage = (this.updateProgress.current / this.updateProgress.total) * 100;
                    this.updateProgress.currentShow = data.show.name;
                    if (this.updateProgress.current != 0) {
                        this.updateProgress.timeRemaining = (((100 - ((this.updateProgress.current / this.updateProgress.total) * 100))
                            * (window.performance.now() - this.updateProgress.timeStarted))
                            / ((this.updateProgress.current / this.updateProgress.total) * 100)) / 1000;
                    } else {
                        this.updateProgress.timeRemaining = 0;
                    }
                });
        }
    },
    mounted: function() {
        this.listenEpisodeUpdated();
        this.listenShowUpdated();
    },
    computed: {
        timeRemaining: function() {
            if (this.updateProgress.timeRemaining == 0) {
                return ' - ';
            } else {
                return Math.round(this.updateProgress.timeRemaining / 60) +
                    "m " +
                    Math.round(this.updateProgress.timeRemaining % 60) +
                    "s left";
            }
        },
    },
    watch: {
    }
});

Vue.component('show-download-result-component', require('./components/ShowDownloadResultComponent.vue').default);
var ShowDownloads = new Vue({
    el: '#ShowDownloads',
    data: {
        active: false,
        downloadResults: [],
    },
    methods: {
        showDownloads: function() {

        },
        listenTorrentAdded: function() {
            window.torrentSocket.on('torrentAdded', (data) => {
                if (!magnetURIChecker(data.infohash)) {
                    return false;
                }
                this.active = true;
                window.axios.get('/torrent/add/' + data.episode_id + '/' + data.infohash.trim())
                    .then(({data}) => {
                        let downloadNew = {
                            fileName: '',
                            show: {
                                uuid: data.data.show.uuid,
                                name:  data.data.show.name,
                            },
                            episode: {
                                uuid: data.data.uuid,
                                episode_code: data.data.episode_code,
                            },
                            infohash: data.data.torrent[0].hash,
                            fetched: true,
                        };

                        this.downloadResults.push(downloadNew);
                    }).catch((error) => {
                    console.log(error.response);
                });
            });
        },
    },
    mounted: function() {
        this.listenTorrentAdded();
    },
    computed: {
    },
});

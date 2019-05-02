
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
    return text.slice(0, stop) + (stop < text.length ? suffix || '...' : '')
})

Vue.component('show-search-result-component', require('./components/ShowSearchResultComponent.vue').default);
var ShowSearch = new Vue({
    el: '#ShowSearch',
    data: {
        showSearchQuery: '',
        results: [],
        active: false,
        noResults: false,
        searching: false,
        searched: false,
    },
    methods: {
        searchShow: function () {
            this.results = [];
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
                        this.results = data.data;
                    }).catch((error) => {
                    console.log(error.response.data);
                });
            } else {
            }
        },
        resetSearch: function() {
            this.results = [];
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
        results: [],
    },
    methods: {
        updateAllShows: function () {
            window.axios.get('/data/update/0')
                .then(({data}) => {
                    this.active = true;
                    Echo.channel('episode-action.' + window.Laravel.user).listen('EpisodeCreated', (user, episode) => {
                        console.log(user);
                        console.log(episode);
                        this.results.push(episode);
                    });
                }).catch((error) => {
                console.log(error.response.data);
            });
        },
    }
});

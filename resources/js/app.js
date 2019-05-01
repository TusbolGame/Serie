
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./components', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('show-search-result-component', require('./components/ShowSearchResultComponent.vue').default);

Vue.filter('truncate', function (text, stop, suffix) {
    return text.slice(0, stop) + (stop < text.length ? suffix || '...' : '')
})

import Vue from 'vue'
var ShowSearch = new Vue({
    el: '#ShowSearch',
    data: {
        showSearchQuery: '',
        results: [],
        active: false,
        noResults: false,
        searching: false
    },
    methods: {
        searchShow: function () {
            if (this.showSearchQuery.trim().length > 0) {
                this.searching = true;
                window.axios.get('/data/search/show/' + this.showSearchQuery)
                    .then(({data}) => {
                        if (data.data.length > 0) {
                            this.active = true;
                        } else {
                            this.active = false;
                            this.noResults = true;
                        }
                        this.searching = false;
                        this.results = data.data;
                    });
            } else {

            }
        },
        addShow: function(api_id) {
            window.axios.get('/data/add/show/' + api_id)
                .then(({data}) => {
                    console.log(data);
                });
        }
    }
});

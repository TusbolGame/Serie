
window._ = require('lodash');
window.bootbox = require('bootbox');
window.moment = require('moment');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    window.Vue = require('Vue');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a generic header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo"
window.io = require('socket.io-client');

// Have this in case you stop running your laravel echo server

if (typeof io !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: 'newserie.local:6001',
        authEndpoint: '/broadcasting/auth',
    });
}

window.Echo.private('data-update.' + window.Laravel.user)
// window.Echo.channel('data-update')
    .listen('EpisodeCreated', (e) => {
    console.log(e);
    console.log(true);
});
window.Echo.connector.socket.on('connect', function(){
    console.log('connected', window.Echo.socketId());
});
window.Echo.connector.socket.on('disconnect', function(){
    console.log('disconnected');
});
window.Echo.connector.socket.on('reconnecting', function(attemptNumber){
    console.log('reconnecting', attemptNumber);
});

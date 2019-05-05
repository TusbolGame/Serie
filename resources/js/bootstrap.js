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

import Echo from 'laravel-echo'

window.io = require('socket.io-client');

// Have this in case you stop running your laravel echo server
if (typeof io !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001',
    });
}

// var baseURL               = getBaseURL(); // Call function to determine it
// var socketIOPort          = 8080;
// var socketIOLocation      = baseURL + socketIOPort; // Build Socket.IO location
// var socket                = io.connect(socketIOLocation);
//
// // Build the user-specific path to the socket.io server, so it works both on 'localhost' and a 'real domain'
// function getBaseURL() {
//     baseURL = window.location.protocol + "//" + window.location.hostname + ":" + window.location.port;
//     return baseURL;
// }

// Torrent settings
window.torrentServer = 'http://localhost:5000/';
var connectionOptions = {
    'force new connection': true,
    'reconnectionAttempts': 'Infinity',
    'timeout': 4000,
    'transports': ['websocket'],
};


window.torrentSocket = window.io(window.torrentServer, connectionOptions);


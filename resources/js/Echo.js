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

// For debugging purposes
window.Echo.connector.socket.on('connect', function(){
    console.log('connected', window.Echo.socketId());
});
window.Echo.connector.socket.on('disconnect', function(){
    console.log('disconnected');
});
window.Echo.connector.socket.on('reconnecting', function(attemptNumber){
    console.log('reconnecting', attemptNumber);
});

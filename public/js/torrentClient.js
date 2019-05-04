var socket = window.io.connect('http://localhost:5000', {
    transport: 'websocket',
});

socket.on('connect', function(data) {
    socket.emit('join', 'Hello World from client');
});

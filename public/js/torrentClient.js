var connectionOptions = {
    'force new connection': true,
    'reconnectionAttempts': 'Infinity',
    'timeout': 4000,
    'transports': ['websocket'],
};

var socketURL = 'http://localhost:5000';


var socket = window.io(socketURL, connectionOptions);

socket.on('connect', function(data) {
    socket.emit('join', 'Hello World from client');
    console.log('Connected to ' + socketURL);
});

socket.on('socketToMe', function(data) {
    console.log(data);
});

socket.on('torrentAdded', function(data) {
    console.log(data);
});

socket.on('torrentReady', function(data) {
    console.log(data);
});

socket.on('disconnect', function(){
    console.log('Disconnected from ' + socketURL);
});

socket.on('disconnect', function(){
    console.log('Disconnected from ' + socketURL);
});

socket.on('torrentFinish', function(data) {

});

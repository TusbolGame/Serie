window.torrentSocket.on('connect', function(data) {
    window.torrentSocket.emit('join', 'magnet:?xt=urn:btih:' + '13afb25d94e6c34f15aae11416c8084368b7ae7c');
    console.log('Connected to ' + window.torrentServer);
});

window.torrentSocket.on('disconnect', function(){
    console.log('Disconnected from ' + window.torrentServer);
});

window.torrentSocket.on('socketToMe', function(data) {
    console.log(data);
});

window.torrentSocket.on('torrentAdded', function(data) {
    var pattern = /([a-fA-F0-9]{40})/g;
    var matches = data.infoHash.trim().match(pattern);
    if (matches == null || matches.length < 1) {
        errorManager('The string provided is not a valid infoHash.');
        console.log('The string provided is not a valid infoHash.');
    }
    // window.axios.get('/torrent/add/' + data.infoHash.trim())
    //     .then(({data}) => {
    //         console.log(true);
    //     }).catch((error) => {
    //     console.log(error.response);
    // });
    console.log(data);
});

window.torrentSocket.on('torrentReady', function(data) {
    console.log(data);
});

window.torrentSocket.on('torrentCompleted', function(data) {

});

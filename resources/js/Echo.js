window.Echo.channel('episode-action.' + window.Laravel.user)
    .listen('EpisodeCreated', (e) => {
        console.log(e);
    });

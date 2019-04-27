function episodeMarkView(container, episode_id, state) {
    if (state != 0 && state != 1 && state != 2 && state != 3) {
        return false;
    }
    var args = {
        url: '/episode/view/mark/' + episode_id + '/' + state,
        method: 'GET'
    };

    ajaxHandler(args,
        function(serverData) {
            container.fadeOut(ANIMATION_TIME, function() {
                $(this).replaceWith(serverData.data);
            });
        }, null
    );
}

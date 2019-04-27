$(document).on('input', '.text-filter-input', function() {
    let input = $(this).val().toLowerCase();
    let container = $(this).parents('.text-filter-container');
    let target = container.find('.text-filter-target');

    $.each(target.children(), function(index, value) {
        if ($(value).attr('data-filter').toLowerCase().indexOf(input) == -1) {
            $(value).fadeOut(ANIMATION_TIME);
        } else {
            $(value).fadeIn(ANIMATION_TIME / 2);
        }
    });
});

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

// Bootstrap tooltip initialization
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})


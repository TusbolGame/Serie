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

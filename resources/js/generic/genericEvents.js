$(document).ready(function() {
    // Ripple handling
    $(document).on('click', '.cmn-rippleable', function(e) {
//        e.preventDefault();
        if ($(this).find('.cmn-button-ripple').length !== 0) {
            rippleHandler(e, $(this).find('.cmn-button-ripple'));
        } else if ($(this).find('.cmn-ripple-container').length !== 0) {
            rippleHandler(e, $(this).find('.cmn-ripple-container'));
        }
    });
});
let ANIMATION_TIME = 400;
let CSS_TRANSITION_NAMES = 'webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend' + ' ' + 'webkitAnimationEnd oanimationend msAnimationEnd animationend';

let DRAG_DROPPABLE = false;
// Drag n Drop
if (window.File && window.FileList && window.FileReader) {
    DRAG_DROPPABLE = true;
} else {
    DRAG_DROPPABLE = false;
}

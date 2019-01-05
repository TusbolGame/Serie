let bookmarkSmallInterval = null;
let bookmarkBigInterval = null;
let BOOKMARK_SMALL_INTERVAL_TIMER = 10000;
let BOOKMARK_BIG_INTERVAL_TIMER = 60000;
let controlsHideInterval;
let CONTROLS_HIDE_INTERVAL_TIMER = 2000;

(function($) {
    $.fn.Stream = function(customOptions) {
        const defaultOptions = {
            controlsHideIntervalTimer: 3000
        };
        let options = $.extend({}, defaultOptions, customOptions);
    };
}(jQuery));

/* Video player functions */
function videoInitialization(video) {
    let videoWrapper = $(video).parents('#video-wrapper');
    volumeHandler(video);
    videoWrapper.find('.controls .time .total').text(timeFormat(video.duration, false));
    videoProgressHandler(video);

    if ($(video).hasAttr("data-bookmark")) {
        bookmark = parseInt($(video).attr("data-bookmark"));
        if (bookmark !== null && $.isNumeric(bookmark)) {
            video.currentTime = bookmark;
        }
    }

    videoWrapper.find('.controls-container').fadeIn(ANIMATION_TIME);
    videoWrapper.find('.info').fadeIn(ANIMATION_TIME);

// page loads starts delay timer
    controlsHideInterval = setInterval(delayCheck, CONTROLS_HIDE_INTERVAL_TIMER);
    //start to get video buffering data
    setTimeout(bufferingHandler, 200);

    //display video buffering bar
    function bufferingHandler() {
        let progressContainer = videoWrapper.find('.controls .progressbar');
        let video = videoWrapper.find('.player video').get(0);
        let currentBuffer = video.buffered.end(0);
        let maxduration = video.duration;
        let percentage = 100 * currentBuffer / maxduration;

        progressContainer.find('.buffer').width(percentage + '%');

        if(currentBuffer < maxduration) {
            setTimeout(bufferingHandler, 500);
        }
    };
}

function delayCheck() {
    $('#video-wrapper').find('.controls').fadeOut(ANIMATION_TIME);
}

function commonStreamActions(actionType, video) {
    let stream = $(video).attr('data-stream');
    let videoViewId = $(video).attr('data-view');
    let args = {
        url: '/ajax/streamAction/' + actionType + '/' + stream + '/' + videoViewId,
        method: 'GET'
    };
    ajaxHandler(args,
        function(serverData) {
            if ($(video).attr('data-view') == 0) {
                $(video).attr('data-view', serverData.data);
            }
        }, null
    );
}

function commonBookmark(video, bookmarkType) {
    let stream = $(video).attr('data-stream');
    let videoViewId = $(video).attr('data-view');
    let time = video.currentTime;
    let args = {
        url: '/ajax/bookmark/' + bookmarkType + '/' + stream + '/' + videoViewId + '/' + timeFormat(time),
        method: 'GET'
    };
    ajaxHandler(args,
        function(serverData) {
            if ($(video).attr('data-view') == 0) {
                $(video).attr('data-view', serverData.data);
            }
        }, null
    );
}

function setSmallBookmarkInterval(video) {
    bookmarkSmallInterval = setInterval(function() {
        commonBookmark(video, 3);
    }, BOOKMARK_SMALL_INTERVAL_TIMER);
}

function setBigBookmarkInterval(video) {
    bookmarkBigInterval = setInterval(function() {
        commonBookmark(video, 4);
    }, BOOKMARK_BIG_INTERVAL_TIMER);
}

function videoFullscreenToggler(button, element) {  // element has to be #video-wrapper
    let currentElementInFullscreen = document.fullscreenElement;
    if (currentElementInFullscreen !== null) {      // Fullscreen currently in use
        document.exitFullscreen();
        swapButton(button, 3, 'default-fullscreen-exit', 'default-fullscreen');
    } else {
        if (element.get(0).requestFullscreen) {
            element.get(0).requestFullscreen();
        }
        swapButton(button, 4, 'default-fullscreen', 'default-fullscreen-exit');
    }
}

function videoStateHandler(button, video) {
    if (video.ended) {
        videoStateToggler(button, 'play'); // Button to play
    } else if (video.paused) {
        video.play();
        videoStateToggler(button, 'pause'); // Button to pause
    } else {
        video.pause();
        videoStateToggler(button, 'play'); // Button to play
    }
}

function videoStateToggler(button, requiredState) {
    switch (requiredState) {
        case 'play':
            swapButton(button, 1, 'default-pause', 'default-play');
            break;
        case 'pause':
            swapButton(button, 2, 'default-play', 'default-pause');
            break;
        default:
            break;
    }
}

function swapButton(button, dataType, removeClass, addClass) {
    let tempTitle = button.attr("title");
    button.attr("title", button.attr("title-alternative"));
    button.attr("title", tempTitle);
    button.attr("data-type", dataType);
    button.find(".cmn-button-content").removeClass(removeClass);
    button.find(".cmn-button-content").addClass(addClass);
}

function skipperHandler(video, direction) {
    if (direction === 'rewind') {
        video.currentTime = (video.currentTime > 10) ? video.currentTime - 10 : 0;
        commonStreamActions(10, video);
    } else if (direction === 'forward') {
        video.currentTime = (video.currentTime + 10 < video.duration) ? video.currentTime + 10 : video.duration;
        commonStreamActions(9, video);
        if (video.currentTime === video.duration) {
            video.trigger("ended");
        }
    }
}

function volumeHandler(video) {
    let level;
    let slider = $(video).parents('#video-wrapper').find('.controls .slider');
    if (slider.hasAttr('data-level')) {
        level = Number.parseFloat(slider.attr('data-level')).toFixed(6);
    } else {
        level = 100;
        slider.find('.actual').width(level + '%');
        slider.attr("data-level", level);
    }
    let volumeContainer = $(video).parents('#video-wrapper').find('.controls .volume');
    let button = $(video).parents('#video-wrapper').find('.controls .muter button');
    video.volume = level / 100;

    // change muter button icon based on volume
    if (video.volume === 0){
        volumeContainer.attr("data-state", "muted");
    } else if (video.volume > 0 && video.volume <= 0.25){
        volumeContainer.attr("data-state", "low");
    } else if (video.volume > 0.25 && video.volume <= 0.5){
        volumeContainer.attr("data-state", "med-low");
    } else if (video.volume > 0.5 && video.volume <= 0.75){
        volumeContainer.attr("data-state", "med-high");
    } else if (video.volume > 0.75 && video.volume <= 0.9){
        volumeContainer.attr("data-state", "high");
    } else {
        volumeContainer.attr("data-state", "max");
    }
}

function barSlideHandler(barContainer, whereClicked) {
    let position = whereClicked - barContainer.find(".background").offset().left;
    let percentage = 100 * position / barContainer.find(".background").width();

    if (percentage > 100) {
        percentage = 100;
    }
    if (percentage < 0) {
        percentage = 0;
    }

    barContainer.find(".actual").width(percentage + '%');
    barContainer.attr('data-level', Number.parseFloat(percentage).toFixed(6));
}

function videoMuterToggler(button, video) {
    let volumeContainer = button.parents('.controls .volume');
    let slider = volumeContainer.find('.slider');
    let newVolume;
    if (!video.muted) {
        newVolume = 0;
    } else {
        if (slider.hasAttr("data-level") && parseInt(slider.attr("data-level")) !== 0) {
            newVolume = parseInt(slider.attr("data-level"));
        } else {
            newVolume = 100;
        }
    }
    commonStreamActions(6, video);
    volumeContainer.attr("data-level", newVolume);
    slider.attr("data-level", newVolume);
    video.muted = !video.muted;
}

function videoProgressHandler(video) {
    let videoWrapper = $(video).parents('#video-wrapper');
    let progressbar = videoWrapper.find('.controls .progressbar');
    let timeContainer = videoWrapper.find('.controls .time');

    timeContainer.find('.elapsed').html(timeFormat(video.currentTime, false));
    videoRemainingTimeHandler(video);
}

function videoRemainingTimeHandler(video, direction) {
    let videoWrapper = $(video).parents('#video-wrapper');
    let timeContainer = videoWrapper.find('.controls .time');

    if (timeContainer.find('.total').hasClass("remaining")) {
        timeContainer.find('.total').html(timeFormat(video.duration - video.currentTime, false));
    }
    if (direction === 'remaining') {
        timeContainer.find('.total').html(timeFormat(video.duration - video.currentTime, false));
        timeContainer.find('.total').addClass('remaining');
    } else if (direction === 'normal') {
        timeContainer.find('.total').html(timeFormat(video.duration, false));
        timeContainer.find('.total').removeClass('remaining');
    }
}

function videoSeeker(video) {
    let progressbar = $(video).parents('#video-wrapper').find('.controls .progressbar');

    let updatedTime = (Number.parseFloat(progressbar.attr('data-level')) / 100) * video.duration;
    video.currentTime = updatedTime;
}

function tooltipHandler(video, e) {
    let videoWrapper = $(video).parents('#video-wrapper');
    let tooltip = $('#video-wrapper').find('.controls .progress-time');


    let position = e.pageX - $('#video-wrapper').find('.controls .progress-container .background').offset().left;

    let time = Math.round((position / $('#video-wrapper').find('.controls .progress-container .background').width()) * video.duration);
    if (time < 0) {
        time = 0;
    } else if (time > video.duration) {
        time = video.duration;
    }

    tooltip.find('.text').text(timeFormat(time, false));

    if (position < tooltip.outerWidth() / 2) {
        position = 0;
    } else if (position > $('#video-wrapper').find('.controls .progress-container .background').width() - tooltip.outerWidth() / 2) {
        position = $('#video-wrapper').find('.controls .progress-container .background').width() - tooltip.outerWidth();
    } else {
        position = position - tooltip.outerWidth() / 2;
    }

    tooltip.css({left: position + 'px'})


    videoWrapper.find('.controls .progress-time').fadeIn(ANIMATION_TIME);
}

function playbackSpeedHandler(video, direction) {
    const speedArray = [0.12, 0.25, 0.50, 0.66, 0.75, 1, 1.2, 1.5, 2, 3, 4, 8, 16];
    const minSpeed = 0;
    let maxSpeed = speedArray.length - 1;
    let curSpeed;

    if ($(video).hasAttr('data-playback-speed')) {
        curSpeed = parseInt($(video).attr("data-playback-speed"));

        if (Math.floor(curSpeed) !== curSpeed || !$.isNumeric(curSpeed)) {
            curSpeed = 5;
        }
    } else {
        curSpeed = 5;
    }

    if (direction === 'increase') {
        curSpeed++;
        if (curSpeed > maxSpeed) {
            curSpeed = maxSpeed;
        }
    } else if (direction === 'decrease') {
        curSpeed--;
        if (curSpeed < minSpeed) {
            curSpeed = minSpeed;
        }
    } else if (direction === 'reset') {
        curSpeed = 5;
    }
    $("#video").attr("data-playback-speed", curSpeed);

    video.playbackRate = speedArray[curSpeed];
}


/* Video player events */
$(document).ready(function() {
    $('#video-wrapper').on('click', '.placeholder .starter', function(e) {
        let video = $('#video').get(0);

        video.play();
    });

    $('#video').on('loadedmetadata', function(e) {
        videoInitialization($('#video').get(0));
    });

    $('#video').on('progress', function(e) {
        let video = $(this).get(0);
        let videoWrapper = $(video).parents('#video-wrapper');
        let percentage;

        if (video.duration > 0) {
            if (video.seekable.end(video.seekable.length - 1) === video.duration) {
                videoWrapper.find('.controls .progressbar .buffer').width(100 + "%");
            } else {
                for (let i = 0; i < video.seekable.length; i++) {
                    if (video.seekable.start(video.seekable.length - 1 - i) < video.currentTime) {
                        percentage = (video.seekable.end(video.seekable.length - 1 - i) / video.duration) * 100;
                        videoWrapper.find('.controls .progressbar .buffer').width(percentage + "%");
                        break;
                    }
                }
            }
        }
    });

    $('#video').on('play', function(e) {
        let button =  $('#video-wrapper').find('.controls .state button');

        commonStreamActions(1, this);
        videoStateToggler(button, 'pause'); // Button to pause

        setSmallBookmarkInterval(this);
        setBigBookmarkInterval(this);

        clearInterval(controlsHideInterval);
        controlsHideInterval = setInterval(delayCheck, CONTROLS_HIDE_INTERVAL_TIMER);

        if ($('#video-wrapper .placeholder .starter').length !== 0) {
            $('#video-wrapper .placeholder .starter').addClass('active');
            $('#video-wrapper .placeholder .starter').fadeOut(ANIMATION_TIME / 2, function () {
                $('#video-wrapper .placeholder .starter').remove();
                $('#video-wrapper').find('.controls-container').fadeIn(ANIMATION_TIME);
                $('#video-wrapper .placeholder').hide();
            });
        }
    });

    $('#video').on('pause', function(e) {
        let button = $('#video-wrapper').find('.controls .state button');

        commonStreamActions(2, this);
        videoStateToggler(button, 'play') // Button to play
        commonBookmark(this, 2);

        if (bookmarkSmallInterval) {
            clearInterval(bookmarkSmallInterval);
        }
        if (bookmarkSmallInterval) {
            clearInterval(bookmarkBigInterval);
        }

        $('#video-wrapper').find('.controls').fadeIn(200);
        clearInterval(controlsHideInterval);
    });

    $('#video-wrapper').on('click', '.controls .secondary .time .elapsed', function() {
        let video = ('#video').get(0);
        commonBookmark(video, 4);
    });

    //video canplaythrough event
    //solve Chrome cache issue
    let completeloaded = false;
    $('#video').on('canplaythrough', function() {
        completeloaded = true;
    });
    /* Progress Bar Events */
    let progressDrag = false;
    $('#video-wrapper').on('mousedown', '.controls .progressbar', function(e) {
        progressDrag = true;
        let video = $(this).parents('#video-wrapper').find('.player video').get(0);

        videoSeeker(video)
        tooltipHandler(video, e);
    });

    $('#video-wrapper').on('mouseup', '.controls .progressbar', function(e) {
        if(progressDrag) {
            progressDrag = false;
        }
        commonBookmark(video, 5);
        commonStreamActions(8, video);
    });

    $(window).on('mousemove', function(e) {
        let video = $('#video-wrapper').find('.player video').get(0);
        if(progressDrag) {
            videoSeeker(video);
            videoProgressHandler(video);
            tooltipHandler(video, e);
        }
    });



    $('#video-wrapper .controls .progressbar').on('mousemove', function(e) {
        let video = $('#video-wrapper').find('.player video').get(0);
        tooltipHandler(video, e);
    });

    $('#video-wrapper .controls .progressbar').on('mouseout', function(e) {
        if(!progressDrag) {
            $('#video-wrapper').find('.controls .progress-time').stop(true).fadeOut(ANIMATION_TIME);
        }
    });

    //video seeking event
    $('#video').on('seeking', function(e) {
        let video = this;

        videoProgressHandler(video);
    });

    //video seeked event
    $('#video').on('seeked', function(e) {
        let video = this;

        videoProgressHandler(video);
    });

    //video waiting for more data event
    $('#video').on('waiting', function(e) {
        let videoWrapper = $(this).parents('#video-wrapper');
        videoWrapper.find('.placeholder').html(createPlaceHolder(0, 'whi')).fadeIn(ANIMATION_TIME);
    });

    $('#video-wrapper').on('dblclick', '.skipper-container', function(e) {
        if(e.target !== e.currentTarget) return;    // If clicked on child and not directly parent

        let videoWrapper = $(this).parents('#video-wrapper');
        let button = videoWrapper.find('.controls .fullscreen button');
        videoFullscreenToggler(button, videoWrapper);
    });

    $('#video-wrapper').on('dblclick', '.skipper', function(e) {
        let skipper = $(this);
        let videoWrapper = skipper.parents('#video-wrapper');

        if (skipper.hasClass('rewind')) {
            skipperHandler(videoWrapper.find('video').get(0), 'rewind');
        } else if (skipper.hasClass('forward')) {
            skipperHandler(videoWrapper.find('video').get(0), 'forward');
        }
    });

    $('#video').on('ended', function(e) {
        let actionType = 7;
        commonStreamActions(actionType, video);
        s(this).parents('#video-wrapper').find('.controls').fadeIn(ANIMATION_TIME);
    });

    $('#video-wrapper').on('fullscreenchange', function(e) {
        e.preventDefault();
        let video = $(this).find('video');
        let button = $(this).find('.controls .fullscreen button');

        let currentElementInFullscreen = document.fullscreenElement;
        if (currentElementInFullscreen !== null) {      // Fullscreen currently in use
            commonStreamActions(3, video);
        } else {
            commonStreamActions(4, video);
        }
    });

    //display current video play time
    $('#video').on('timeupdate', function() {
        let video = $(this).get(0);
        let videoWrapper = $(video).parents('#video-wrapper');

        let percentage = Number.parseFloat(Number.parseFloat(video.currentTime / video.duration) * 100).toFixed(6);
        videoWrapper.find('.controls .progressbar .actual').width((percentage) + '%');
        videoWrapper.find('.controls .progressbar').attr('data-level', percentage);
        videoProgressHandler(video);
    });

    // Playback speed change
    $('#video').on('ratechange', function() {
        let video = $(this).get(0);
        console.log(video.playbackRate);
    });

    /* Slider Bar update events */
    let slideBarDrag = {
        status: false,
        which: ''
    };
    $('.bar').on('mousedown', function(e) {
        slideBarDrag.status = true;
        slideBarDrag.which = $(this);
        barSlideHandler($(this), e.pageX);
    });

    $(window).on('mouseup', function(e) {
        if(slideBarDrag.status) {
            slideBarDrag.status = false;
        }
    });

    $(window).on('mousemove', function(e) {
        if(slideBarDrag.status) {
            barSlideHandler(slideBarDrag.which, e.pageX);
        }
    });

    /* Volume Bar Events */
    let volumeDrag = false;
    $('#video-wrapper').on('mousedown', '.controls .volume .slider', function(e) {
        volumeDrag = true;
        let video = $(this).parents('#video-wrapper').find('.player video').get(0);
        volumeHandler(video);
    });

    $(window).on('mouseup', function(e) {
        if(volumeDrag) {
            volumeDrag = false;
            let video = $('#video-wrapper').find('.player video').get(0);
            volumeHandler(video);
            commonStreamActions(6, video);
        }
    });

    $(window).on('mousemove', function(e) {
        if(volumeDrag) {
            let video = $('#video-wrapper').find('.player video').get(0);
            volumeHandler(video);
        }
    });

    $('#video-wrapper').on('click', '.controls .time .total', function(e) {
        let video = $('#video-wrapper').find('video').get(0);
        let direction;

        if ($(this).hasClass('remaining')) {
            direction = 'normal';
        } else {
            direction = 'remaining';
        }
        videoRemainingTimeHandler(video, direction);
    });

    $('#video-wrapper').on('mousemove', function(e) {
        $('#video-wrapper').find('.controls').fadeIn(ANIMATION_TIME / 2);
        if (!$('#video-wrapper video').get(0).paused) {
            clearInterval(controlsHideInterval);
            controlsHideInterval = setInterval(delayCheck, CONTROLS_HIDE_INTERVAL_TIMER);
        }
    });

    $('#video-wrapper').on('keypress', function(e) {
        let videoWrapper = $('#video-wrapper');
        let video = videoWrapper.find('.player video').get(0);
        let button;
        if (!e.ctrlKey) {
            switch (e.key) {
                case ' ':        // Spacebar Behaviour
                    e.preventDefault();
                    button = videoWrapper.find('.controls .state button');
                    videoStateHandler(button, video);
                    break;
                case 'ArrowLeft':       // Arrow Left Behaviour
                    e.preventDefault();
                    skipperHandler(video, 'rewind');
                    break;
                case 'ArrowRight':       // Arrow Right Behaviour
                    e.preventDefault();
                    skipperHandler(video, 'forward');
                    break;
                case 'f':       // F Behaviour
                    e.preventDefault();
                    button = videoWrapper.find('.controls .fullscreen button');
                    videoFullscreenToggler(button, videoWrapper);
                    break;
                case 's':       // S Behaviour
                    video.currentTime = 0;
                    video.pause();
                    e.preventDefault();
                    break;
                case '+':       // Numpad Add Behaviour
                    e.preventDefault();
                    playbackSpeedHandler(video, 'increase');
                    break;
                case '-':       // Numpad Subtract Behaviour
                    e.preventDefault();
                    playbackSpeedHandler(video, 'decrease');
                    playbackSpeedHandler(video, 'reset');
                    break;
                case 'r':       // R Behaviour
                    e.preventDefault();
                    playbackSpeedHandler(video, 'reset');
                    break;
                default:
                    break;
            }
        }
    });

    $('#video-wrapper').on('click', '.video-container .controls button', function() {
        let button = $(this);
        let buttonType = parseInt(button.attr('data-type'));
        let videoWrapper = button.parents('#video-wrapper');
        let video = videoWrapper.find('video').get(0);

        switch (buttonType) {
            case 1: // play
                videoStateHandler(button, video);
                break;
            case 2: // pause
                videoStateHandler(button, video);
                break;
            case 3: // enter fullscreen
                videoFullscreenToggler(button, videoWrapper);
                break;
            case 4: // Exit fullscreen
                videoFullscreenToggler(button, videoWrapper);
                break;
            case 6: // volume
                videoMuterToggler(button, video);
                break;
            case 9: // forward 10
                skipperHandler(video, 'forward');
                break;
            case 10: // replay 10
                skipperHandler(video, 'rewind');
                break;
            default:
                break;
        }
    });
});


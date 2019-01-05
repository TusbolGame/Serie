function ajaxCaller(args) {
    let ajaxOptions = setDefaultOptions(args);
    return $.ajax({
        url: ajaxOptions.url,
        headers: ajaxOptions.headers,
        method: ajaxOptions.method,
        data: ajaxOptions.data,
        accepts: ajaxOptions.accepts,
        dataType: ajaxOptions.dataType,
        async: ajaxOptions.async,
        success: ajaxOptions.success
    });

    function setDefaultOptions(args) {
        let data = (args.data === null && typeof args.data === undefined) ? args : args.data;
        let defaultAjaxOptions = {
            url: '/ajax',
            method: 'POST',
            dataType: 'json',
            accepts: {
                xml: 'text/xml',
                text: 'text/plain'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            async: true,
            cache: false,
            data: data
        };
        return $.extend({}, defaultAjaxOptions, args);
    }
}

function defaultAjaxErrorCallback(error, ajaxErrorCallback = null) {
    if (ajaxErrorCallback != null && typeof serverData  !== 'undefined') {
        ajaxErrorCallback(serverData);
    } else {
        if (error != null && typeof error  !== 'undefined') {
            if (error.responseJSON != null && typeof error.responseJSON  !== 'undefined') {
                console.log(error.responseJSON);
            }
        } else {
            console.log(error);
        }
    }
}

function defaultAjaxSuccessCallback(serverData, successCallbackFunction) {
    if (serverData !== null && typeof serverData  !== 'undefined') {
        if (serverData.data != null && typeof serverData.data  !== 'undefined') {
            if (successCallbackFunction != null) {
                successCallbackFunction(serverData);
            }
        } else {
            console.log('ServerData data is null');
            console.log(serverData.data);
        }
        console.log('ServerData is null');
        console.log(serverData);
    }
}

function ajaxHandler(args, ajaxSuccessCallback = null, ajaxErrorCallback = null) {
    $.when(ajaxCaller(args)).then(
        function(serverData) {
            if (ajaxSuccessCallback != null) {
                defaultAjaxSuccessCallback(serverData, ajaxSuccessCallback);
            }
        }, function (serverData) {  // Might return error or return data on error to be handled
            if (ajaxErrorCallback != null) {    // If I'm expecting an error
                defaultAjaxErrorCallback(serverData, ajaxErrorCallback);
            } else {    // If I'm NOT expecting an error
                defaultAjaxErrorCallback(serverData);
            }
        }
    );
}

function timeFormat(seconds, hours) {
    let h = Math.floor(seconds / 3600);
    let m = Math.floor(seconds % 3600 / 60);
    let s = Math.floor(seconds % 3600 % 60);

    let string = "";

    if (typeof hours === 'undefined') {
        string = (Math.abs(h < 10) ? "0" + h : h) + ":";
    } else {
        if (h > 0) {
            string = h + ":";
        } else {
            string = "";
        }
    }
    string = string + (Math.abs(m < 10) ? "0" + m : m) + ":" + (Math.abs(s < 10) ? "0" + s : s);

    return string;
}

function rippleHandler(event, container) {
    let distance;

    if (container.find(".cmn-ripple").length === 0) {
        container.append('<div class="cmn-ripple"></div>');
    }
    let ripple = container.find(".cmn-ripple");
    ripple.removeClass("animate");

    //set size of .ink
    if(!ripple.height() && !ripple.width()){
        distance = Math.max(container.width(), container.height());
        ripple.css({height: distance, width: distance});
    }
    let x = event.pageX - container.offset().left - ripple.width() / 2;
    let y = event.pageY - container.offset().top - ripple.height() / 2;

    ripple.css({top: y+'px', left: x+'px'}).addClass("animate");
    ripple.one(CSS_TRANSITION_NAMES, function(){
        ripple.remove();
    });
}

function createPlaceHolder(type, colorClass) {
    let container = $('<div class="cmn-loader-container"></div>');
    if (typeof colorClass !== "undefined") {
        container.addClass(colorClass);
    } else {
        container.addClass("c2");
    }
    
    if (type === 0) {
        container.addClass("cmn-cube-grid");
        for (var i = 1; i <= 9; i++) {
            container.append('<div class="cmn-cube sk-cube'+i+'"></div>');
        }
    } else if (type === 1) {
        container.addClass("loading");
    }
    return container;
}

hideLoader = function() {
    let parent = $(this);
    parent.find(".cmn-loader-container").remove();
};

// check if element has an attribute or if the attribute equals the value provided
$.fn.hasAttr = function(name, value) {
    let attr = $(this).attr(name);
    // For some browsers, `attr` is undefined; for others,
    // `attr` is false.  Check for both.
    if (typeof attr !== typeof undefined && attr !== false) {
        if (typeof value !== "undefined" && value !== null) {
            if (attr === value) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return false;
    }
    return typeof attr !== typeof undefined && attr !== false;
};

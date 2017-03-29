"use strict";

/**
 * Globally exposed namespacing function.
 * @param {string} namespace
 */
function namespace(namespace) {
    var object = window, tokens = namespace.split("."), token;

    while (tokens.length > 0) {
        token = tokens.shift();

        if (typeof object[token] === "undefined") {
            object[token] = {};
        }

        object = object[token];
    }

    return object;
}


/**
 * Globally exposed extending function.
 * @param {array} parent prototypes
 * @param {hash} children
 */
function extend(target, source) {
    target = target || {};
    for (var prop in source) {
        if (typeof source[prop] === 'object') {
            target[prop] = extend(target[prop], source[prop]);
        } else {
            target[prop] = source[prop];
        }
    }
    return target;
}

function inherit(parents, child, properties) {

    var childPrototype = properties;

    for (var i in parents) {
        var parentPrototype = Object.create(parents[i].prototype);
        childPrototype = extend(childPrototype, parentPrototype);
    }

    child.prototype = childPrototype;
    child.prototype.constructor = child;

    return child;
}


/**
 * Globally filters out jQuery elements matching selector
 * from the haystack
 * @param {*} selector
 * @param {*} haystack
 */
function filter(selector, haystack) {
    var matches = [],
        i = -1;

    while (++i < haystack.length) {
        if (haystack[i].element && haystack[i].element.is('selector')) {
            matches.push(haystack[i]);
        }
    }

    return matches;
}

(function (undefined) {

    "use strict";

    var App = namespace("Tmt").App = function() {
        this.profile = null;
        this.initializers = [];
        this.events = [
            "ready"
        ];
    };

    inherit([ Evemit ], App, {
        'init': function (userdata) {
            this.profile = new Tmt.Models.Profile(userdata);
            prepareInitializers.call(this);
            this.emit("ready");
        }
    });

    function prepareInitializers() {
        // Create an intance of each initializer.
        for(var type in Tmt.Initializers) {
            this.initializers[type] = new Tmt.Initializers[type]();
        }

        // Run the initialization. This is done in two steps because
        // initializers may depend on one another.
        for(var type in this.initializers) {
            this.initializers[type].build(this);
        }
    }

}());

(function (undefined) {

    "use strict";

    var Profile = namespace("Tmt.Models").Profile = function(userData) {
        this.events = [
            "upvoteUpdate"
        ];

        for(var i in userData) {
            this[i] = userData[i];
        }
    };

    inherit([ Evemit ], Profile, {

    });

}());

(function() {

    "use strict";

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function(el) {
        this.element = el;
        this.events = [
            'onBeforeSubmit',
            'onBound',
            'onRender',
            'onSubmit'
        ];
        this.listeners = {
            'onBeforeSubmit' : [],
            'onBound' : [],
            'onRender' : [],
            'onSubmit' : []
        };
    };

    inherit([ Evemit ], AjaxForm, {

        addListener : function(key, callback) {
            this.listeners[key].push(callback);
        },

        fireEvent : function (key) {
            if (this.listeners[key]) {
                for( var i = 0, len = this.listeners[key].length; i < len; i++) {
                    this.listeners[key][i]();
                }
            }
        },

        init : function() {
            this.addEvents();
        },

        addEvents : function() {
            this.element.on("submit", onSubmit.bind(this));
            this.addListener("onBeforeSubmit", onBeforeSubmit.bind(this));
            this.fireEvent('onBound', [this]);
        }
    });


    function onSubmit(event) {
        event.preventDefault();

        this.fireEvent("onBeforeSubmit", [this, event]);

        var formElement = this.element;

        $.ajax({
            url: formElement.attr("action"),
            data: new FormData(formElement.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: formElement.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.fireEvent("onSubmit", [this]);
    };

    function onBeforeSubmit()
    {
        this.element.addClass("working");
    }

    function onSubmitSuccess(html) {
        var newVersion = $(html);

        this.element.replaceWith(newVersion);
        this.element = newVersion;
        this.addEvents();

        this.fireEvent("afterRender", [this]);
    }

}());

(function($, undefined) {

    "use strict";

    var Player = namespace("Tmt.Components").Player = function(element) {
        this.element = element;
        this.embed = null;
        this.ytPlayer = null;
        this.events = [
            'play',
            'stop'
        ];
        this.isPlaying = false;
        this.range = null;
    };

    inherit([ Evemit ], Player, {
        'init' : function() {
            this.hasVideoId() ?
                embedVideo.call(this) :
                queryForKey.call(this);
        },

        'getEmbedId' : function() {
            if (this.hasVideoId) {
                return "tmt_player_" + this.getVideoId();
            }
        },

        'getVideoId' : function() {
            return this.element.data("song-vid");
        },

        'setVideoId' : function(id) {
            this.element.data("song-vid", id);
        },

        'hasVideoId' : function() {
            return this.getVideoId() != "";
        },

        'getSongSlug' : function() {
            return this.data("song-slug");
        }
    });

    function queryForKey() {
        $.getJSON('/ajax/ytkey/' + this.getSongSlug(), onYtKeyReceived(response).bind(this));
    }

    function onYtKeyReceived(response) {
        if (response.youtubekey.length === 11) {
            this.setVideoId(response.youtubekey);
            embedVideo.call(this);
        }
    }

    function embedVideo() {
        var id = this.getEmbedId();
        var iframeHtml =
            '<iframe id="' + id + '" scrolling="no" marginwidth="0" ' +
                'marginheight="0" frameborder="0" src="//www.youtube.com/embed/' +
                this.getVideoId() + '?enablejsapi=1&amp;iv_load_policy=3&amp;' +
                'playerapiid=songplayer_component_17&amp;disablekb=1&amp;wmode=transparent&amp;controls=0' +
                '&amp;playsinline=0&amp;showinfo=0&amp;modestbranding=1&amp;rel=0&amp;' +
                'autoplay=0&amp;loop=0&amp;origin=' + window.location.origin + '"></iframe>'

        this.element.append(iframeHtml);
        this.embed = $("#" + id);

        this.ytPlayer = new YT.Player(id);

        this.ytPlayer.addEventListener('onReady', onPlayerReady.bind(this));
        this.ytPlayer.addEventListener('onStateChange', onPlayerStateChange.bind(this));
    }

    function onPlayerStateChange(newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video queued) */
        var controlButton = this.element.find('.play');

        if (newState.data === 1) {
            controlButton.removeClass("fa-play");
            controlButton.addClass("fa-pause");

            this.isPlaying = true;
            onPlayerTick.call(this);
        }
        else if (newState.data === 2) {
            controlButton.removeClass("fa-pause");
            controlButton.addClass("fa-play");

            this.isPlaying = false;
        }
    }

    function onProgressClick(e) {
        if (this.isPlaying) {
            var progressBar = this.element.find(".progress-wrap .progress");
            var offset = progressBar.offset();
            var relX = e.pageX - offset.left;
            var pctLocation = relX / progressBar.width();
            player.seekTo(pctLocation * this.ytPlayer.getDuration(), true);
        }
    };

    function onPlayBtnClick(e) {
        // Ranges wil be back shortly
        this.playingRange = null;

        (this.ytPlayer.getPlayerState() != 1) ?
            this.ytPlayer.playVideo() :
            this.ytPlayer.pauseVideo();
    }


    function onPlayerReady(event) {
        this.element.find(".duration").html(toReadableTime(this.ytPlayer.getDuration()));
        this.element.find(".position").html(toReadableTime(0));
        this.element.find(".progress-wrap .progress").click(onProgressClick.bind(this));

        var playBtn = this.element.find('.play');
        playBtn.removeClass("fa-stop");
        playBtn.addClass("fa-play");
        playBtn.click(onPlayBtnClick.bind(this));

        // Ranges wil be back shortly
        // $("*[data-from]").click(function () {
        //     var el = $(this);
        //     tmt.playingRange = [parseInt(el.attr("data-from"), 10), parseInt(el.attr("data-to"), 10)];
        //     (player.getPlayerState() != 1) ? player.playVideo() : player.pauseVideo();
        //     player.seekTo(tmt.playingRange[0], true);
        // });
    };

    function onPlayerTick() {
        var currentTime = this.ytPlayer.getCurrentTime(),
            durationTime = this.ytPlayer.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        this.element.find('.position').html(toReadableTime(currentTime));

        this.element.find('.cursor').css("left", currentPositionPct + "%");
        this.element.find('.progress .loaded-bar').css("width", (this.ytPlayer.getVideoLoadedFraction() * 100) + "%");
        this.element.find('.progress .playing-bar').css("width", currentPositionPct + "%");
        this.element.find('.progress .playing-bar').attr("aria-valuenow", currentTime);

        if (this.isPlaying) {
            // if (tmt.playingRange) {

            //     if (currentTime >= tmt.playingRange[1]) {
            //         tmt.playingRange = null;
            //         player.pauseVideo();
            //     }
            //     else if (currentTime <= tmt.playingRange[0]) {
            //         player.seekTo(tmt.playingRange[0], true);
            //     }
            // }
            setTimeout(onPlayerTick.bind(this), 250);
        }
    }

    function toReadableTime(seconds) {
        var time = new Date(1000 * seconds),
            mins = ("0" + time.getMinutes()).slice(-2),
            secs = ("0" + time.getSeconds()).slice(-2);

        return mins + ":" + secs;
    }

})(jQuery);

(function($, undefined) {

    "use strict";


    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function() {
        this.events = [];
    };

    inherit([ Evemit ], UpvoteForm, {

    });


})(jQuery);

(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function() {
        this.forms = [];
        this.events = [
            "bound"
        ];
    };

    inherit([ Evemit ], AjaxFormsInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });

    function bindPageForms() {
        var forms = [];

        $("form[data-ctrl-mode=ajax]").each(function(){
            var form = new Tmt.Components.AjaxForm($(this));
            form.init();
            forms.push(form);
        });

        this.forms = forms;
        this.emit('bound', this);
    }

    function addEvents(app) {
        app.on('ready', bindPageForms.bind(this));
    }

}(jQuery));

(function($, undefined) {

    "use strict";

    var PlayerInitializer = namespace("Tmt.Initializers").PlayerInitializer = function() {
        this.events = [
            "bound",
            "youtubeBound"
        ];
        this.players = [];
    };

    inherit([ Evemit ], PlayerInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        $(onDomReady.bind(this));
    }

    function onDomReady() {
        var components = $("*[data-song-vid]");
        if (components.length > 0) {
            for(var i = 0; i < components.length; i++) {
                this.players.push(new Tmt.Components.Player($(components.get(i))));
            }
            includeYoutubeScript.call(this);
        }
        this.emit('bound', this);
    }

    function onYouTubeReady() {
        for(var i = 0; i < this.players.length; i++) {
            this.players[i].init();
        }
    }

    function includeYoutubeScript() {
        var tag = document.createElement('script');
        tag.src = "//www.youtube.com/player_api";

        window.onYouTubeIframeAPIReady = onYouTubeReady.bind(this);

        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

}(jQuery));

(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var UpvoteFormsInitializer = namespace("Tmt.Initializers").UpvoteFormsInitializer = function() {
        this.boxes = [];

        this.events = [
            "bound"
        ];
    };

    inherit([ Evemit ], UpvoteFormsInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });


    function addEvents(app) {
        app.initializers.AjaxFormsInitializer.on('bound', bindToAjaxForms.bind(this));
        app.profile.on('upvoteUpdate', updateBoxesState.bind(this));
    }

    function bindToAjaxForms(AjaxFormsInitializer) {
        var upvoteForms = filter('[data-ctrl="upvote-widget"]', AjaxFormsInitializer.forms);
        for (var i = 0, len = upvoteForms.length; i < len; i++) {
            this.boxes.push(new Tmt.Components.UpvoteForm(upvoteForms[i]));
        }

        this.emit('bound', this);
    }

    function updateBoxesState(newValues) {

    }

}(jQuery));

//# sourceMappingURL=app.js.map

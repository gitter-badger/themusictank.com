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
function extend(parents, child) {
    for (var i in parents) {
        for(var k in parents[i].prototype) {
            child[i] = parents[i].prototype[k];
        }
    }

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

    var App = namespace("Tmt").App = function App() {
        this.profile = null;
        this.initializers = [];
    };

    App.prototype = extend([ Evemit ], {

        'init': function (userdata) {
            this.profile = new tmt.Profile(userdata);
            prepareInitializers.bind(this);
            this.emit("init");
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
            this.initializers[type].build(app);
        }

    }

}());

jQuery(function () {

    var $ = window.jQuery;

    $("*[data-song-vid]").each(function () {
        var el = $(this),
            videoId = el.attr("data-song-vid");

        if (videoId != "") {
            appendVideoAndScript(el, videoId);
        } else {
            queryForKey(el, el.attr("data-song-slug"));
        }
    });

    function queryForKey(el, url) {
        $.getJSON('/ajax/ytkey/' + url, function (response) {
            if (response.youtubekey.length === 11) {
                appendVideoAndScript(el, response.youtubekey);
            }
        });
    }

    function appendVideoAndScript(el, videoId) {
        el.append('<iframe id="songplayer_youtube_api" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" src="//www.youtube.com/embed/' + videoId + '?enablejsapi=1&amp;iv_load_policy=3&amp;playerapiid=songplayer_component_17&amp;disablekb=1&amp;wmode=transparent&amp;controls=0&amp;playsinline=0&amp;showinfo=0&amp;modestbranding=1&amp;rel=0&amp;autoplay=0&amp;loop=0&amp;origin=' + window.location.origin + '"></iframe>');

        var tag = document.createElement('script');
        tag.src = "//www.youtube.com/player_api";

        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    tmt.onPlayerStateChange = function (newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video cued) */
        var el = $('*[data-attr=tmt-player] .play'),
            player = this;

        if (newState.data === 1) {
            el.removeClass("fa-play");
            el.addClass("fa-pause");
            tmt.playerIsPlaying = true;
            tmt.playerTick(player);
        }
        else if (newState.data === 2) {
            el.removeClass("fa-pause");
            el.addClass("fa-play");
            tmt.playerIsPlaying = false;
        }
    }

    tmt.onPlayerReady = function (event) {
        var player = this,
            streamer = $('*[data-attr=tmt-player]'),
            play = streamer.find('.play');

        streamer.find(".duration").html(_toReadableTime(player.getDuration()));
        streamer.find(".position").html(_toReadableTime(0));

        streamer.find(".progress-wrap .progress").click(function (e) {
            if (tmt.playerIsPlaying) {
                var offset = $(this).offset();
                var relX = e.pageX - offset.left;
                var pctLocation = relX / $("*[data-attr=tmt-player] .progress-wrap .progress").width();
                player.seekTo(pctLocation * player.getDuration(), true);
            }
        });

        play.removeClass("fa-stop");
        play.addClass("fa-play");

        play.click(function () {
            tmt.playingRange = null;
            (player.getPlayerState() != 1) ?
                player.playVideo() :
                player.pauseVideo();
        });

        $("*[data-from]").click(function () {
            var el = $(this);
            tmt.playingRange = [parseInt(el.attr("data-from"), 10), parseInt(el.attr("data-to"), 10)];
            (player.getPlayerState() != 1) ? player.playVideo() : player.pauseVideo();
            player.seekTo(tmt.playingRange[0], true);
        });
    };

    tmt.playerTick = function (player) {

        var currentTime = player.getCurrentTime(),
            durationTime = player.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        $('*[data-attr=tmt-player] .position').html(_toReadableTime(currentTime));

        $('*[data-attr=tmt-player] .cursor').css("left", currentPositionPct + "%");
        $('*[data-attr=tmt-player] .progress .loaded-bar').css("width", (player.getVideoLoadedFraction() * 100) + "%");
        $('*[data-attr=tmt-player] .progress .playing-bar').css("width", currentPositionPct + "%");
        $('*[data-attr=tmt-player] .progress .playing-bar').attr("aria-valuenow", currentTime);

        if (tmt.playerIsPlaying) {

            if (tmt.playingRange) {

                if (currentTime >= tmt.playingRange[1]) {
                    tmt.playingRange = null;
                    player.pauseVideo();
                }
                else if (currentTime <= tmt.playingRange[0]) {
                    player.seekTo(tmt.playingRange[0], true);
                }
            }

            setTimeout(function () { tmt.playerTick(player) }, 200);
        }
    };

    function _toReadableTime(seconds) {
        var time = new Date(1000 * seconds),
            mins = ("0" + time.getMinutes()).slice(-2),
            secs = ("0" + time.getSeconds()).slice(-2);

        return mins + ":" + secs;
    }
});

function onYouTubePlayerAPIReady() {
    var player = new YT.Player('songplayer_youtube_api');
    player.addEventListener('onReady', tmt.onPlayerReady.bind(player));
    player.addEventListener('onStateChange', tmt.onPlayerStateChange.bind(player));
}


(function($, TMT, undefined) {

    "use strict";

    TMT.Components.Upvotes = function(instances) {
        var i = -1,
            upvotes = [];

        while (++i < instances.length) {
            var upvote = new Upvote(instances[i]);
            upvotes.push(upvote);
        };

        return upvotes;
    };

    var Upvote = function(form) {
        this.form = form;
    };

    Upvote.prototype = {

    };

})(jQuery, tmt);

//# sourceMappingURL=app.js.map

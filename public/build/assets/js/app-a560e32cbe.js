(function () {

    "use strict";

    // Setup app namespacing
    window.tmt = {
        'Components': {}
    };

    var App = window.tmt.App = function App(data) {
        this.userData = data;
    }

    App.prototype = {
        'init': function () {
            var forms = tmt.Components.AjaxForms();

            var upvotes = tmt.Components.Upvotes(
                filter('[data-ctrl="upvote-widget"]', forms),
                this.userData.upvotes || []
            );
        }
    };

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

})();

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

    var AjaxForms = TMT.Components.AjaxForms = function() {
        var forms = [];

        $("form[data-ctrl-mode=ajax]").each(function(){
            var form = new AjaxForm($(this));
            form.init();
            forms.push(form);
        });

        return forms;
    };

    var AjaxForm = function(el) {
        this.element = el;
        this.listeners = {
            'onBeforeSubmit' : [],
            'onBound' : [],
            'onRender' : [],
            'onSubmit' : []
        };
    };

    AjaxForm.prototype = {

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
    };


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

})(jQuery, tmt);

(function($, TMT, undefined) {

    "use strict";

    TMT.Components.Upvotes = function(instances, userdata) {
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

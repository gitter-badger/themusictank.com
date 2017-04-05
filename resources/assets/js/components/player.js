(function ($, undefined) {

    "use strict";

    var rootNode,
        ready = false,
        embed = null,
        ytPlayer = null,
        isPlaying = false,
        canSkip = true,
        range = null;

    var Player = namespace("Tmt.Components").Player = function (element) {
        rootNode = element;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], Player, {
        'isReady' : function() {
            return ready;
        },

        'getStreamer' : function() {
            return ytPlayer;
        },

        'render': function () {
            this.hasVideoId() ?
                embedVideo.call(this) :
                queryForKey.call(this);
        },

        'getEmbedId': function () {
            if (this.hasVideoId()) {
                return "tmt_player_" + this.getVideoId();
            }
        },

        'getVideoId': function () {
            return rootNode.data("song-vid");
        },

        'setVideoId': function (id) {
            rootNode.data("song-vid", id);
        },

        'hasVideoId': function () {
            return this.getVideoId() != "";
        },

        'getSongSlug': function () {
            return rootNode.data("song-slug");
        }
    });

    function queryForKey() {
        $.getJSON('/ajax/ytkey/' + this.getSongSlug(), onYtKeyReceived.bind(this));
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

        rootNode.append(iframeHtml);
        embed = $("#" + id);

        ytPlayer = new YT.Player(id);
        ytPlayer.addEventListener('onReady', onPlayerReady.bind(this));
        ytPlayer.addEventListener('onStateChange', onPlayerStateChange.bind(this));

        ready = true;
        this.emit("embeded", this);
    }

    function onPlayerStateChange(newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video queued) */
        var controlButton = rootNode.find('.play');

        if (newState.data === 1) {
            controlButton.removeClass("fa-play");
            controlButton.addClass("fa-pause");

            isPlaying = true;
            this.emit("play");
            onPlayerTick.call(this);
        }
        else if (newState.data === 2) {
            controlButton.removeClass("fa-pause");
            controlButton.addClass("fa-play");

            isPlaying = false;
            this.emit("stop");
        }
    }

    function onProgressClick(e) {
        if (isPlaying) {
            var progressBar = rootNode.find(".progress-wrap .progress"),
                offset = progressBar.offset(),
                relX = e.pageX - offset.left,
                pctLocation = relX / progressBar.width();
            ytPlayer.seekTo(pctLocation * ytPlayer.getDuration(), true);
        }
    };

    function onPlayBtnClick(e) {
        // Ranges wil be back shortly
        this.playingRange = null;

        (ytPlayer.getPlayerState() != 1) ?
            ytPlayer.playVideo() :
            ytPlayer.pauseVideo();
    }


    function onPlayerReady(event) {
        rootNode.find(".duration").html(toReadableTime(ytPlayer.getDuration()));
        rootNode.find(".position").html(toReadableTime(0));
        rootNode.find(".progress-wrap .progress").click(onProgressClick.bind(this));

        var playBtn = rootNode.find('.play');
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
        var currentTime = ytPlayer.getCurrentTime(),
            durationTime = ytPlayer.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        rootNode.find('.position').html(toReadableTime(currentTime));

        rootNode.find('.cursor').css("left", currentPositionPct + "%");
        rootNode.find('.progress .loaded-bar').css("width", (ytPlayer.getVideoLoadedFraction() * 100) + "%");
        rootNode.find('.progress .playing-bar').css("width", currentPositionPct + "%");
        rootNode.find('.progress .playing-bar').attr("aria-valuenow", currentTime);

        if (isPlaying) {
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

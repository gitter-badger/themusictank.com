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

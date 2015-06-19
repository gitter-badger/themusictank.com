$(function() {

    // Automate song fetch
    $("*[data-song]").each(function() {
        var el = $(this),
            url = el.attr("data-song");

        $.getJSON('/ajax/yt_key/' + url, function(response) {
            if(response.youtube_key.length === 11) {
                appendVideoAndScript(el, response.youtube_key);
            }
        });
    });

    // Automate song playing
    $("*[data-song-vid]").each(function(){
        var el = $(this);
        appendVideoAndScript(el, el.attr("data-song-vid"));

    });


    function appendVideoAndScript(el, videoId) {
        el.append('<iframe id="songplayer_youtube_api" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" src="//www.youtube.com/embed/'+videoId+'?enablejsapi=1&amp;iv_load_policy=3&amp;playerapiid=songplayer_component_17&amp;disablekb=1&amp;wmode=transparent&amp;controls=0&amp;playsinline=0&amp;showinfo=0&amp;modestbranding=1&amp;rel=0&amp;autoplay=0&amp;loop=0&amp;origin='+window.location.origin+'"></iframe>');

        var tag = document.createElement('script');
        tag.src = "//www.youtube.com/player_api";

        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    tmt.onPlayerStateChange = function(newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video cued) */
        var el = $('.streamer .play'),
            player = this;

        if(newState.data === 1) {
            el.removeClass("fa-play");
            el.addClass("fa-pause");
            tmt.playerIsPlaying = true;
            tmt.playerTick(player);
        }
        else if(newState.data === 2) {
            el.removeClass("fa-pause");
            el.addClass("fa-play");
            tmt.playerIsPlaying = false;
        }
    }

    tmt.onPlayerReady = function(event) {
        var player = this,
            streamer = $('.streamer'),
            play = streamer.find('.play');

        streamer.find(".duration").html(_toReadableTime(player.getDuration()));
        streamer.find(".position").html(_toReadableTime(0));

        streamer.find(".progress-wrap .progress").click(function(e){
            if(tmt.playerIsPlaying) {
                var offset = $(this).offset();
                var relX = e.pageX - offset.left;
                var pctLocation = relX / $(".streamer .progress-wrap .progress").width();
                player.seekTo( pctLocation *  player.getDuration(), true );
            }
        });

        play.removeClass("fa-stop");
        play.addClass("fa-play");

        play.click(function() {
            tmt.playingRange = null;
            (player.getPlayerState() != 1) ?
                player.playVideo() :
                player.pauseVideo();
        });

        $("*[data-from]").click(function(){
            var el = $(this);
            tmt.playingRange = [parseInt(el.attr("data-from"), 10), parseInt(el.attr("data-to"), 10)];
            (player.getPlayerState() != 1) ? player.playVideo() : player.pauseVideo();
            player.seekTo(tmt.playingRange[0], true);
        });
    };

    tmt.playerTick = function(player) {

        var currentTime = player.getCurrentTime(),
            durationTime = player.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        $('.streamer .position').html(_toReadableTime(currentTime));

        $('.streamer .cursor').css("left", currentPositionPct + "%");
        $('.streamer .progress .loaded-bar').css("width", (player.getVideoLoadedFraction() * 100) + "%");
        $('.streamer .progress .playing-bar').css("width", currentPositionPct + "%");
        $('.streamer .progress .playing-bar').attr("aria-valuenow", currentTime);

        if(tmt.playerIsPlaying) {

            if(tmt.playingRange) {

                if (currentTime >= tmt.playingRange[1]) {
                    tmt.playingRange = null;
                    player.pauseVideo();
                }
                else if(currentTime <= tmt.playingRange[0]) {
                    player.seekTo(tmt.playingRange[0], true);
                }
            }

            setTimeout(function(){ tmt.playerTick(player) }, 200);
        }
    };

    function _toReadableTime(seconds) {
        var time = new Date(1000*seconds),
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

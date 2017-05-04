export default class YtStreamer {
    constructor(videoId, autoplay) {
        this.videoId = videoId
        this.id = "tmt_player_" + videoId;
        this.yt = null;
        this.state = -1;
        this.loaded = false;
        this.included = false;

        this.playWhenReady = autoplay;
        this.shouldTick = false;

        this.position = 0;
        this.duration = 0;
        this.bufferedPct = 0;
    }

    init() {
        includeYoutubeScript.call(this);
    }

    play() {
        this.yt.playVideo();
        this.shouldTick = true;
        playerTick.call(this);
    }

    pause() {
        this.yt.pauseVideo();
        this.shouldTick = false;
    }

    seek(time) {
        this.yt.seekTo(time, true);
        this.position = time;
    }

    toggle() {
        if (this.isPlaying()) {
            return this.pause();
        }

        return this.play();
    }

    isPlaying() {
        return this.state == 1;
    }

    isCompleted() {
        return this.state === 0;
    }

    isLoaded() {
        return this.loaded;
    }

    render(nodeDestination) {
        !this.included ?
            includeYoutubeScript.call(this, nodeDestination) :
            injectIframe.call(this, nodeDestination);
    }
};

function playerTick() {
    this.position = this.yt.getCurrentTime();
    this.duration = this.yt.getDuration();
    this.bufferedPct = this.yt.getVideoLoadedFraction() * 100;

    if (this.shouldTick) {
        setTimeout(() => playerTick.call(this), 450);
    }
}

function injectIframe(nodeDestination) {
    nodeDestination.prepend(
        '<iframe id="' + this.id + '" scrolling="no" marginwidth="0" ' +
        'marginheight="0" frameborder="0" src="//www.youtube.com/embed/' +
        this.videoId + '?enablejsapi=1&amp;iv_load_policy=3&amp;' +
        'playerapiid=songplayer_component_17&amp;disablekb=1&amp;wmode=transparent&amp;controls=0' +
        '&amp;playsinline=0&amp;showinfo=0&amp;modestbranding=1&amp;rel=0&amp;' +
        'autoplay=0&amp;loop=0&amp;origin=' + window.location.origin + '"></iframe>'
    );

    this.yt = new YT.Player(this.id); // exposed in 'window' through a script include in the html.
    this.yt.addEventListener('onStateChange', (newState) => { this.state = newState.data });
    this.yt.addEventListener('onReady', () => {
        this.loaded = true
        if (this.playWhenReady) {
            this.play();
        }
    });
}

function includeYoutubeScript(nodeDestination) {
    var tag = document.createElement('script');
    tag.src = "//www.youtube.com/player_api";

    window.onYouTubeIframeAPIReady = () => { injectIframe.call(this, nodeDestination) };

    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
}

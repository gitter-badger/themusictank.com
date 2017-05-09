<script>
import ComponentBase from '../mixins/base.js'
import ProgressGroup from './ProgressGroup.vue'
import TimeLabel from './TimeLabel.vue'
import Reviewer from '../Reviewer/Reviewer.vue'
import YtStreamer from '../../models/yt-streamer.js'

export default {
    components: {
        ProgressGroup, TimeLabel, Reviewer
    },
    mixins: [ComponentBase],

    props: [
        'songSlug',
        'songVideo',
        'songName',
        'isReview',
        'autoplay',
        'profileSlug',
        'albumName'
    ],

    computed: {

        seekable() {
            return !this.isReview;
        },

        isPlaying() {
            return this.streamer && this.streamer.isPlaying();
        },

        isCompleted() {
            return this.streamer && this.streamer.isCompleted();
        },

        songIsLoaded() {
            return this.streamer && this.streamer.isLoaded();
        },

        position() {
            return this.streamer && this.streamer.position;
        }
    },

    data () {
        return {
            'streamer' : null
        }
    },

    mounted() {
        this.songVideo == "" ? getVideoId.call(this) : loadStreamer.call(this);
    },

    methods: {
        seek(position) {
            if (this.seekable) {
                this.streamer.seek(position);
            }
        },

        onPlay() {
            this.streamer.toggle();
        }
    }
};

function getVideoId() {
    this.ajax()
        .post('/ajax/track/ytkey/', this.songSlug)
        .then((response) => {
            if (response.youtubekey.length === 11) {
                this.songVideo = response.youtubekey;
                loadStreamer.bind(this);
            } else {
                Tmt.app.error(response);
            }
        })
        .catch((error) => {
            Tmt.app.error(error);
        });
}

function loadStreamer() {
    this.streamer = new YtStreamer(this.songVideo, this.autoplay);
    this.streamer.render(this.getElement());
}
</script>


<template>
    <div class="ctrl ctrl-player"
        :class="{
            'has-reviewer': isReview
        }">
        <reviewer
            v-if="isReview && streamer && profileSlug"
            :song-name="songName"
            :song-slug="songSlug"
            :profile-slug="profileSlug"
            :album-name="albumName"
            :completed="isCompleted"
            :enabled="isPlaying"
            :position="position"
        ></reviewer>

        <div class="ui" v-if="this.streamer">
            <progress-group
                :total-position-units="streamer.duration"
                :total-buffered-units="100"
                :current-position-progress="streamer.position"
                :current-buffered-progress="streamer.bufferedPct"
                v-on:seek="seek"
            ></progress-group>

            <button class="play" @click.prevent="onPlay" :disabled="!songIsLoaded">
                <i class="fa fa-stop" v-if="!songIsLoaded"></i>
                <i class="fa fa-play" v-if="!isPlaying && songIsLoaded"></i>
                <i class="fa fa-pause" v-if="isPlaying && songIsLoaded"></i>
            </button>

            <div class="times">
                <time-label class="position" :time="streamer.position"></time-label> /
                <time-label class="duration" :time="streamer.duration"></time-label>
            </div>
        </div>
    </div>
</template>


<style lang="scss">
.ctrl-player {
    background: #eee;
    border: 1px solid #dedede;
    border-top: 1px solid #fff;
    position: relative;
    height: 62px;
    font-size: 11px;
    color: #333;

    iframe {
        width: 1px;
        height: 1px;
        position: absolute;
        left: -100px;
    }

    .play {
        cursor: pointer;
        position: absolute;
        left: 10px;
        top: 10px;
        background: #000;
        color: #fff;
        width: 40px;
        height: 40px;
        line-height: 40px;
        z-index: 1;
        font-size: 24px;
        text-align: center;
        border-radius: 40px;
        border: 1px solid #eee;
        box-shadow: 0 1px 5px rgba(0, 0, 0, .6);
        text-indent: 5px;
        overflow: hidden;
    }

    .fa-pause {
        text-indent: 0;
    }

    .fa-stop {
        text-indent: 0;
    }

    .times {
        position: absolute;
        right: 10px;
        top: 25px;
        width: 75px;
        text-align: right;
    }

    .progress {
        height: 5px;
        background: #ccc;
    }

    .progress-wrap {
        position: absolute;
        top: 30px;
        left: 80px;
        right: 80px;
        cursor: pointer;
    }

    .loaded-bar {
        background: orange;
        left: 0;
        position: absolute;
        height: 5px;
    }

    .playing-bar {
        left: 0;
        position: absolute;
        height: 5px;
        background: cyan;
    }

    .cursor {
        background: #fff;
        box-shadow: 0 1px 3px #000;
        border-radius: 20px;
        width: 20px;
        height: 20px;
        position: absolute;
        top: 2px;
        margin: -10px 0 0 -10px;
    }
}
</style>

<script>
import ComponentBase from '../mixins/base.js'
import ProgressGroup from './ProgressGroup.vue'
import TimeLabel from './TimeLabel.vue'

export default {
    components : {
        ProgressGroup, TimeLabel
    },
    mixins: [ComponentBase],

    props: ['songSlug', 'songVideo'],

    computed: {
        songLength() {
            if (this.youtube) {
                return this.youtube.getDuration()
            }

            return 0;
        }
    },

    data() {
        return {
            'seekable': true,
            'isPlaying': false,
            'songIsLoaded': false,
            'currentPosition': 0,
            'loadedProgress': 0
        }
    },

    methods: {


    }
};
</script>


<template>
    <div class="ctrl ctrl-player">
        <progress-group :total-units="songLength" :currentPositionProgress="currentPosition" :currentLoadedProgress="loadedProgress"></progress-group>

        <button class="play" :disabled="!songIsLoaded">
            <i class="fa fa-stop" v-if="!songIsLoaded"></i>
            <i class="fa fa-play" v-if="isPlaying"></i>
            <i class="fa fa-pause" v-if="!isPlaying && songIsLoaded"></i>
        </button>

        <div class="times">
            <time-label class="position" :time="currentPosition"></time-label> / <time-label class="duration" :time="songLength"></time-label>
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

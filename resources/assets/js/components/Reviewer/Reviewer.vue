<script>
import ComponentBase from '../mixins/base.js'
import FxCanvas from '../Canvas/FxCanvas.vue'
import Knob from './Knob.vue'

import KnobModel from '../../models/review/knob.js'
import ReviewerModel from '../../models/review/reviewer.js'

const NEUTRAL_GROOVE_POINT = 0.500,
    GROOVE_DECAY = 0.0005,
    FRAMERATE = 26,
    FRAMES_PER_SAVE = 10 * FRAMERATE,
    HIGH_GROOVE_THRESHOLD = 0.98,
    LOW_GROOVE_THRESHOLD = 0.02,
    LENGTH_TO_SHAKE = 0.65 * FRAMERATE,
    LENGTH_PER_SHAKE = 1.75 * FRAMERATE;

export default {
    components: {
        FxCanvas, Knob
    },
    mixins: [ComponentBase],
    props: ['songName', 'songSlug', 'profileSlug', 'albumName', 'completed', 'enabled', 'position'],
    watch: {
        enabled(val) { val ? this.reviewer.start() : this.reviewer.stop(); }
    },
    render() {
        this.reviewer = new ReviewerModel(
            this,
            this.$refs.knob,
            this.$refs.canvas
        );
        this.reviewer.start();
    },
    data() {
        return {
            'reviewer' : null,
         /*   'shaking' : false,
            'synchronising' : false,
            'completed' : false,
            'timers' : {
                'highGrooveStart': null,
                'lowGrooveStart': null,
            },
            'currentFrameId' : 0,
            'drawnFrameId' : null,
            'savedFrameIdx' : 0,
            'currentGroove' : 0,
            'grooveCurve' : [],*/
        }
    }
};
</script>

<template>
    <div class="ctrl ctrl-reviewer">
        <fx-canvas
            v-ref:canvas
        ></fx-canvas>
        <knob
            v-ref:knob
            :enabled="enabled"
            ></knob>
        <completed-dialogs
            v-if="completed"
            :profile-slug="profileSlug"
            :next-song-slug="nextSongSlug"
            :next-song-name="nextSongName"
            :song-name="songName"
            :album-name="albumName"
        ></completed-dialogs>
    </div>
</template>


<style lang="scss">
.ctrl-player.reviewer {
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    height: auto;
    z-index: 2;
    background: #fff;

    .ui {
        position: absolute;
        bottom: 70px;
        left: 0;
        right: 0;
        background: transparent;
        border: none;
        z-index: 2;
    }
}

.ctrl-reviewer {
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    z-index: 2;

    canvas {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1;
    }

    .knob-track {
    position: absolute;
    top: 50%;
    left: 50%;
    max-width: 75px;
    width: 10%;
    height: 80%;
    max-height: 450px;
    background: #111;
    border: 1px solid #333;
    display: block;
    transform: translate(-50%, -50%);
    overflow: hidden;
    z-index: 2;
    border-radius: 72px;

    b {
        transition: transform .3s;
        background: #666;
        border: 1px solid #333;
        box-shadow: 0 2px 7px #000;
        display: block;
        border-radius: 72px;
        width: 72px;
        height: 72px;
        position: absolute;
    }


    &.enabled b {
        background: #eee;
        cursor: pointer;
    }
}
}
</style>

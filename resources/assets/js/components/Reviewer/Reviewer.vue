<script>
import ComponentBase from '../mixins/base.js'
import FxCanvas from '../Canvas/FxCanvas.vue'
// import Score from './Score.vue'
import Knob from './Knob.vue'

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
    props: ['songName', 'songSlug', 'profileSlug', 'albumName', 'completed', 'working', 'position'],
    watch: {
        working(val) {
            if (val) {
                tick.call(this);
                animate.call(this);
            }
        }
    },
    computed: {
        isPositive() {
            return this.currentGroove > NEUTRAL_GROOVE_POINT;
        },

        isNegative() {
            return this.currentGroove < NEUTRAL_GROOVE_POINT;
        }
    },
    data() {
        return {
            saving: false,
            nextSongSlug: null,
            nextSongName: null,
            timers : {
                highGrooveStart: null,
                lowGrooveStart: null
            },
            shaking : false,
            synchronising : false,
            currentFrameId : 0,
            drawnFrameId : null,
            savedFrameIdx : 0,
            currentGroove : NEUTRAL_GROOVE_POINT,
            grooveCurve : []
        }
    }
};


function tick() {
    if (this.working) {
        setFrameContext.call(this);
        calculateTimelineContext.call(this);
        calculateGroove.call(this);

        if (this.currentFrameId % FRAMES_PER_SAVE === 0) {
            saveGrooveCurve.call(this);
        }

        logCurrentFrame.call(this);

        setTimeout(tick.bind(this), 1000 / FRAMERATE);
    }
}

function animate() {
    if (this.drawnFrameId != this.currentFrameId) {
        this.drawnFrameId = this.currentFrameId;
        paintFrame.call(this);
    }
    requestAnimationFrame(animate.bind(this));
}

function setFrameContext() {
    this.currentFrameId++;

    if (this.currentFrameId > 100000) {
        this.currentFrameId = 1;
    }
}

function calculateTimelineContext() {
    // if (this.knob.isWorking()) {
        if (this.currentGroove > HIGH_GROOVE_THRESHOLD) {
            this.timers.lowGrooveStart = null;
            calculatePositiveContext.call(this);
            return;
        } else if (this.currentGroove < LOW_GROOVE_THRESHOLD) {
            this.timers.highGrooveStart = null;
            calculateNegativeContext.call(this);
            return;
        }
    // }

    this.timers.lowGrooveStart = null;
    this.timers.highGrooveStart = null;
    this.shaking = false;
}

// liking it a lot
function calculatePositiveContext() {
    if (!this.timers.highGrooveStart) {
        this.timers.highGrooveStart = this.currentFrameId;
        this.shaking = true;
        // this.canvas.emit("positiveSpark", 10);

    } else if (this.timers.highGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
        this.timers.highGrooveStart = null;
        this.currentGroove = HIGH_GROOVE_THRESHOLD;
        this.knob.stopCurrentDrag();
        this.shaking = false;
        // this.canvas.emit("positiveSpark", 100);
    }
}

// hating it a lot
function calculateNegativeContext() {
    if (!this.timers.lowGrooveStart) {
        this.timers.lowGrooveStart = this.currentFrameId;
        this.shaking = true;
        // this.canvas.emit("negativeSpark", 10);

    } else if (this.timers.lowGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
        this.timers.lowGrooveStart = null;
        this.currentGroove = LOW_GROOVE_THRESHOLD;
        this.knob.stopCurrentDrag();
        this.shaking = false;
        // this.canvas.emit("negativeSpark", 100);
    }
}


function calculateGroove() {

    /*if (this.knob.isWorking()) {
        this.currentGroove = this.knob.getValue();
    } else */if (this.isPositive) {
        this.currentGroove -= GROOVE_DECAY;
    } else if (this.isNegative) {
        this.currentGroove += GROOVE_DECAY;
    }

    if (
        this.currentGroove > (NEUTRAL_GROOVE_POINT - (GROOVE_DECAY * 2)) &&
        this.currentGroove < (NEUTRAL_GROOVE_POINT + (GROOVE_DECAY * 2))
    ) {
        this.currentGroove = NEUTRAL_GROOVE_POINT;
    }

    // this.knob.value = this.currentGroove;
}

function logCurrentFrame() {
    if (this.currentFrameId % 5 === 0) {
        // Save the current frame only if the value is different than
        // the previous one. This should save a lot of unecessary DB
        // entries.
        var currentFrame = {
            groove: this.currentGroove.toFixed(5),
            position: this.position.toFixed(3)
        },
            previousFrame = this.grooveCurve.length > 0 ? this.grooveCurve[this.grooveCurve.length - 1] : null;

        if (!previousFrame || currentFrame.groove != previousFrame.groove) {
            this.grooveCurve.push(currentFrame);
        }
    }
}


function paintFrame() {
    // if (this.shaking) {
    //     this.knob.nudge();
    // } else {
    //     this.knob.center();
    // }

    // this.canvas.draw();
}

</script>

<template>
    <div class="ctrl ctrl-reviewer">
        <fx-canvas></fx-canvas>
        <knob
            :enabled="working"
            :value="currentGroove"
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
.ctrl-player {
    position:absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    height:auto;
    z-index: 2;
    background: #fff;

    .ui {
        position:absolute;
        bottom: 70px;
        left:0;
        right: 0;
        background:transparent;
        border:none;
        z-index: 2;
    }
}

.ctrl-reviewer {
    position:absolute;
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
}
</style>

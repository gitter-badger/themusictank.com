
const NEUTRAL_GROOVE_POINT = 0.500,
    GROOVE_DECAY = 0.0005,
    FRAMERATE = 26,
    FRAMES_PER_SAVE = 10 * FRAMERATE,
    HIGH_GROOVE_THRESHOLD = 0.98,
    LOW_GROOVE_THRESHOLD = 0.02,
    LENGTH_TO_SHAKE = 0.65 * FRAMERATE,
    LENGTH_PER_SHAKE = 1.75 * FRAMERATE;

export default class Reviewer {

    constructor(component, knob) {
        this.component = component;
        this.knob = knob;

        this.shaking = false;
        this.synchronising = false;
        this.completed = false;
        this.highGrooveStart = null;
        this.lowGrooveStart = null;
        this.currentFrameId = 0;
        this.drawnFrameId = null;
        this.savedFrameIdx = 0;
        this.currentGroove = null;
        this.grooveCurve = [];

    }

    start() {
        if (this.currentGroove == null) {
            this.setGrooveTo(NEUTRAL_GROOVE_POINT);
        }

        this.enabled = true;

        tick.call(this);
        animate.call(this);
    }

    stop() {
        this.enabled = false;
    }

    setGrooveTo(value) {
        this.currentGroove = value;
        this.knob.value = value;
    }

    isPositive() {
        return this.currentGroove > NEUTRAL_GROOVE_POINT;
    }

    isNegative() {
        return this.currentGroove < NEUTRAL_GROOVE_POINT;
    }

    hasUnsynchronisedFrames() {
        return (this.savedFrameIdx + 1) < this.grooveCurve.length;
    }

    save() {
        if (
            this.grooveCurve.length > 0 && // ... has frames to save
            this.hasUnsynchronisedFrames() && // ... and more values have been added since the last time
            !this.component.synchronising // ... but is not currently saving
        ) {
            // Limit the size of sent packages
            var packageTotal = this.grooveCurve.length;
            if (packageTotal > 150) {
                packageTotal = 150;
            }

            this.component.sendFramesPackage(this.grooveCurve.slice(this.savedFrameIdx, packageTotal));
            this.savedFrameIdx = packageTotal - 1;
        }
    }

}

function tick() {
    if (this.enabled) {
        setFrameContext.call(this);
        calculateTimersContext.call(this);
        calculateGroove.call(this);

        if (this.currentFrameId % FRAMES_PER_SAVE === 0) {
            this.save();
        }

        logCurrentFrame.call(this);

        setTimeout(tick.bind(this), 1000 / FRAMERATE);
    }
}

function setFrameContext() {
    this.currentFrameId++;

    if (this.currentFrameId > 100000) {
        this.currentFrameId = 1;
    }
}

function calculateTimersContext() {
    if (this.knob.isDragging()) {
        if (this.currentGroove > HIGH_GROOVE_THRESHOLD) {
            this.lowGrooveStart = null;
            calculatePositiveContext.call(this);
            return;
        } else if (this.currentGroove < LOW_GROOVE_THRESHOLD) {
            this.highGrooveStart = null;
            calculateNegativeContext.call(this);
            return;
        }
    }

    this.lowGrooveStart = null;
    this.highGrooveStart = null;
    this.shaking = false;
}

function calculateGroove() {
    if (this.knob.isDragging()) {
        this.currentGroove = this.knob.valueFromPosition;
        return;
    }

    if (this.isPositive()) {
        this.currentGroove -= GROOVE_DECAY;
    } else if (this.isNegative(this)) {
        this.currentGroove += GROOVE_DECAY;
    }

    if (
        this.currentGroove > (NEUTRAL_GROOVE_POINT - (GROOVE_DECAY * 2)) &&
        this.currentGroove < (NEUTRAL_GROOVE_POINT + (GROOVE_DECAY * 2))
    ) {
        this.currentGroove = NEUTRAL_GROOVE_POINT;
    }

    this.knob.value = this.currentGroove;
}


// liking it a lot
function calculatePositiveContext() {
    if (!this.highGrooveStart) {
        this.highGrooveStart = this.currentFrameId;
        this.shaking = true;

    } else if (this.highGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
        this.highGrooveStart = null;
        this.currentGroove = HIGH_GROOVE_THRESHOLD;
        this.knob.stopCurrentDrag();
        this.shaking = false;
    }
}

// hating it a lot
function calculateNegativeContext() {
    if (!this.lowGrooveStart) {
        this.lowGrooveStart = this.currentFrameId;
        this.shaking = true;

    } else if (this.lowGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
        this.lowGrooveStart = null;
        this.currentGroove = LOW_GROOVE_THRESHOLD;
        this.knob.stopCurrentDrag();
        this.shaking = false;
    }
}

function animate() {
    if (this.drawnFrameId != this.currentFrameId) {
        this.drawnFrameId = this.currentFrameId;
        paintFrame.call(this);
    }
    requestAnimationFrame(animate.bind(this));
}

function paintFrame() {
    if (this.shaking) {
        this.knob.nudge();
    } else if (this.knob.nudged) {
        this.knob.center();
    }
}

function logCurrentFrame() {
    if (this.currentFrameId % 5 === 0) {
        // Save the current frame only if the value is different than
        // the previous one. This should save a lot of unecessary DB
        // entries.
        var currentFrame = {
            groove: this.currentGroove.toFixed(5),
            position: this.component.position.toFixed(3)
        },
            previousFrame = this.grooveCurve.length > 0 ? this.grooveCurve[this.grooveCurve.length - 1] : null;

        if (!previousFrame || currentFrame.groove != previousFrame.groove) {
            this.grooveCurve.push(currentFrame);
        }
    }
}


/*
function onSyncFail() {
    this.component.synchronising = false;
}

function proposeNextSong() {
    $.ajax("/ajax/" + this.trackSlug + "/getNext/", {
        type: "POST",
        dataType: "json",
        cache: false,
        complete: (data) => {
            this.component.synchronising = false;
            let nextSong = data.responseJSON;
            if (nextSong.length > 0) {
                this.component.songSlug = nextSong[0].slug;
                this.component.songName = nextSong[0].name;
            }
        }
    });
}

function onNextSong(data) {
    var nextSong = data.responseJSON;
    var next = this.rootNode.find('.next-step');
    next.removeClass("review-still-saving");

    if (nextSong.length > 0) {
        next.addClass("next-track");
        next.find("i").replaceWith('<a href="/tracks/' + nextSong[0].slug + '/review/">' + nextSong[0].name + '</a>')
    } else {
        next.addClass("nothing-else");
    }
}
*/

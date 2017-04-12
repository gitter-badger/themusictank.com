(function ($, undefined) {

    "use strict";

    var NEUTRAL_GROOVE_POINT = 0.500,
        GROOVE_DECAY = 0.0005,
        FRAMERATE = 26,
        FRAMES_PER_SAVE = 10 * FRAMERATE,
        HIGH_GROOVE_THRESHOLD = 0.98,
        LOW_GROOVE_THRESHOLD = 0.02,
        LENGTH_TO_SHAKE = 0.65 * FRAMERATE,
        LENGTH_PER_SHAKE = 1.75 * FRAMERATE;


    var Reviewer = namespace("Tmt.Components.Reviewer").Reviewer = function (element, playerObj) {
        this.rootNode = element;
        this.player = playerObj;
        this.player.canSkip = false;

        this.shaking = false;
        this.synchronising = false;
        this.completed = false;

        this.timers = {
            highGrooveStart: null,
            lowGrooveStart: null,
        };

        this.currentFrameId = 0;
        this.drawnFrameId = null;
        this.savedFrameIdx = 0;
        this.trackSlug = element.find("[data-song-slug]").data("song-slug");

        this.currentGroove = 0;
        this.grooveCurve = [];

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Reviewer, {

        'initialize': function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            registerKnob.call(this);
            registerCanvas.call(this);
            addEvents.call(this);
            setGrooveTo.call(this, NEUTRAL_GROOVE_POINT);
            start.call(this);
        }
    });

    function addEvents() {
        this.player.on("play", onPlay.bind(this));
        this.player.on("stop", onStop.bind(this));
        this.player.on("completed", onComplete.bind(this));
        this.player.on("progressClickEvent", function(){ return false; });
    }

    function setGrooveTo(value) {
        this.currentGroove = value;
        this.knob.setValue(value);
    }

    function start() {
        this.player.getStreamer().playVideo();
    }

    function onPlay() {
        this.knob.enable();
        tick.call(this);
        animate.call(this);
    }

    function onStop() {
        this.knob.disable();
    }

    function onComplete() {
        onStop.call(this);
        this.completed = true;
        this.rootNode.addClass("review-completed");

        if (hasUnsynchronisedFrames.call(this)) {
            saveGrooveCurve.call(this);
        } else {
            proposeNextSong.call(this);
        }
    }

    function registerKnob() {
        this.knob = new Tmt.Components.Reviewer.Knob(this.rootNode.find(".knob-track"));
    }

    function registerCanvas() {
        this.canvas = new Tmt.Components.Reviewer.Canvas(this.rootNode.find("canvas"));

        this.canvas.addEmitter("positiveSpark", this.canvas.node.width / 2, this.canvas.node.height * .15);
        this.canvas.addEmitter("negativeSpark", this.canvas.node.width / 2, this.canvas.node.height * .85);
    }

    function tick() {
        if (this.player.isPlaying()) {
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

    function isPositive() {
        return this.currentGroove > NEUTRAL_GROOVE_POINT;
    }

    function isNegative() {
        return this.currentGroove < NEUTRAL_GROOVE_POINT;
    }

    function setFrameContext() {
        this.currentFrameId++;

        if (this.currentFrameId > 100000) {
            this.currentFrameId = 1;
        }
    }

    function paintFrame() {
        if (this.shaking) {
            this.knob.nudge();
        } else {
            this.knob.center();
        }

        this.canvas.draw();
    }

    function calculateGroove() {
        if (this.knob.isWorking()) {
            this.currentGroove = this.knob.getValue();
        } else if (isPositive.call(this)) {
            this.currentGroove -= GROOVE_DECAY;
        } else if (isNegative.call(this)) {
            this.currentGroove += GROOVE_DECAY;
        }

        if (
            this.currentGroove > (NEUTRAL_GROOVE_POINT - (GROOVE_DECAY * 2)) &&
            this.currentGroove < (NEUTRAL_GROOVE_POINT + (GROOVE_DECAY * 2))
        ) {
            this.currentGroove = NEUTRAL_GROOVE_POINT;
        }

        this.knob.setValue(this.currentGroove);
    }

    function calculateTimelineContext() {
        if (this.knob.isWorking()) {
            if (this.currentGroove > HIGH_GROOVE_THRESHOLD) {
                this.timers.lowGrooveStart = null;
                calculatePositiveContext.call(this);
                return;
            } else if (this.currentGroove < LOW_GROOVE_THRESHOLD) {
                this.timers.highGrooveStart = null;
                calculateNegativeContext.call(this);
                return;
            }
        }

        this.timers.lowGrooveStart = null;
        this.timers.highGrooveStart = null;
        this.shaking = false;
    }

    // liking it a lot
    function calculatePositiveContext() {
        if (!this.timers.highGrooveStart) {
            this.timers.highGrooveStart = this.currentFrameId;
            this.shaking = true;
            this.canvas.emit("positiveSpark", 10);

        } else if (this.timers.highGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.highGrooveStart = null;
            this.currentGroove = HIGH_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.shaking = false;
            this.canvas.emit("positiveSpark", 100);
        }
    }

    // hating it a lot
    function calculateNegativeContext() {
        if (!this.timers.lowGrooveStart) {
            this.timers.lowGrooveStart = this.currentFrameId;
            this.shaking = true;
            this.canvas.emit("negativeSpark", 10);

        } else if (this.timers.lowGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.lowGrooveStart = null;
            this.currentGroove = LOW_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.shaking = false;
            this.canvas.emit("negativeSpark", 100);
        }
    }

    function logCurrentFrame() {
        if (this.currentFrameId  % 5 === 0) {
            // Save the current frame only if the value is different than
            // the previous one. This should save a lot of unecessary DB
            // entries.
            var currentFrame = {
                    groove: this.currentGroove.toFixed(5),
                    position: this.player.getStreamer().getCurrentTime().toFixed(3)
                },
                previousFrame = this.grooveCurve.length > 0 ? this.grooveCurve[this.grooveCurve.length - 1] : null;

            if (!previousFrame || currentFrame.groove != previousFrame.groove) {
                this.grooveCurve.push(currentFrame);
            }
        }
    }

    function saveGrooveCurve() {

        if (
            this.grooveCurve.length > 0 && // ... has frames to save
            hasUnsynchronisedFrames.call(this) && // ... and more values have been added since the last time
            !this.synchronising // ... but is not currently saving
        ) {

            // Limit the size of sent packages
            var packageTotal = this.grooveCurve.length;
            if (packageTotal > 150) {
                packageTotal = 150;
            }

            sendFramesPackage.call(this, this.grooveCurve.slice(this.savedFrameIdx, packageTotal));
            this.savedFrameIdx = packageTotal - 1;
        }
    }

    function sendFramesPackage(span) {
        this.synchronising = true;

        $.ajax("/ajax/" + this.trackSlug + "/saveCurvePart/", {
            type: "POST",
            cache: false,
            dataType: "json",
            data: { 'package': span },
            success: onSyncSuccess.bind(this),
            error: onSyncFail.bind(this)
        });
    }

    function onSyncSuccess() {
        this.synchronising = false;

        // When the song is completed, loop up to the moment when all
        // the frames have been saved
        if (this.completed) {
            finishUpReviewSave.call(this);
        }
    }

    function finishUpReviewSave() {
        this.rootNode.find('.next-step').addClass("review-still-saving");

        if (hasUnsynchronisedFrames.call(this)) {
            saveGrooveCurve.call(this);
        } else {
            proposeNextSong.call(this);
        }
    }

    function hasUnsynchronisedFrames() {
        return (this.savedFrameIdx + 1) < this.grooveCurve.length;
    }

    function onSyncFail() {
        this.synchronising = false;
    }

    function proposeNextSong() {
        $.ajax("/ajax/" + this.trackSlug + "/getNext/", {
            type: "POST",
            dataType: "json",
            cache: false,
            complete: onNextSong.bind(this)
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


})(jQuery);

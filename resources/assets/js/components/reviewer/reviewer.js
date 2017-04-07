(function ($, undefined) {

    "use strict";


    // var GROOVE_DECAY_VALUE  = 0.0005,
    //     MIDDLE_GROOVE_VALUE = 0.500,
    //     FRAMERATE           = 24,
    //     FRAMES_PER_SAVE     = null,

    //     SAVE_FRAMERATE       = 3 / FRAMERATE * 1000,
    //     MAX_MULTIPLIER      = 3,

    //     // These define the length of all 'animations'
    //     DURATION_COMPARE    = 3.5 * 60, // the basis for all effects is based on 1 sec when song is 3m30s
    //     ,
    //     //LENGTH_POWERING     = 4.2,
    //     LENGTH_SHAKING      = 1.8,
    //     //LENGTH_TO_MULTIPLIER = 3,
    //     //LENGTH_MULTIPLYING  = 3.5,

    //     //FRAMES_PER_POWERING = null,
    //     FRAMES_TO_SHAKE     = null,
    //     FRAME_PER_SHAKE     = null
    //     //FRAMES_PER_MULTIPLIER  = null,
        //FRAMES_PER_MULTIPLY = null,

       // HIGH_GROOVE_MULTIPLIER_THRESHOLD = .75,
        //LOW_GROOVE_MULTIPLIER_THRESHOLD = .25,
        //HIGH_GROOVE_THRESHOLD = .98,
        //LOW_GROOVE_THRESHOLD = .02;
;


// var //currentGroove = MIDDLE_GROOVE_VALUE,
//     decay = 0,
//     multiplier = 0,
//     reviewFrames = [],
//     appHasFocus = true,
//     savetick = 0,
//     lastframeSent = 0,
//     songIsOver = false,

//     domCache = [];

    var NEUTRAL_GROOVE_POINT = 0.500,
        GROOVE_DECAY        = 0.0005,
        FRAMERATE           = 28,
        SAVE_FRAMERATE      = 3 / FRAMERATE * 1000,
        HIGH_GROOVE_THRESHOLD = 0.98,
        LOW_GROOVE_THRESHOLD = 0.02,
        LENGTH_TO_SHAKE     = 0.75 * FRAMERATE,
        LENGTH_PER_SHAKE    = 2 * FRAMERATE;


    var Reviewer = namespace("Tmt.Components.Reviewer").Reviewer = function (element, playerObj) {
        this.rootNode = element;
        this.player = playerObj;
        this.isShaking = false;

        this.timers = {
            highGrooveStart : null,
            lowGrooveStart : null,
        };
        this.currentGroove = 0;
        this.currentFrameId = 0;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Reviewer, {

        'initialize' : function() {
            Tmt.EventEmitter.prototype.initialize.call(this);

            registerKnob.call(this);
            registerEmitter.call(this);
            addEvents.call(this);
            setGrooveTo.call(this, NEUTRAL_GROOVE_POINT);
            start.call(this);
        }
    });

    function addEvents()
    {
        this.player.on("play", onPlay.bind(this));
        this.player.on("stop", onStop.bind(this));
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
    }

    function onStop() {
        this.knob.disable();
    }

    function registerKnob() {
        this.knob = new Tmt.Components.Reviewer.Knob(this.rootNode.find(".knob-track"));
    }

    function registerEmitter() {
        this.emitter = new Tmt.Components.Reviewer.Emitter.ParticleEmitter(this.rootNode.find("i"));
        this.emitter.moveTo(this.knob.position.left, this.knob.position.top);
        this.emitter.initialize(40);
    }

    function tick() {
        if (this.player.isPlaying()) {
            setFrameContext.call(this);
            calculateTimelineContext.call(this);
            calculateGroove.call(this);
            paintFrame.call(this);
        }

        this.emitter.tick();
        setTimeout(tick.bind(this), 1000 / FRAMERATE);
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
        this.emitter.animate();

        // dont update if user is still dragging
        if (!this.knob.isWorking()) {
            this.knob.setValue(this.currentGroove);
        }

        (this.isShaking) ?
            this.knob.nudge() :
            this.knob.center();
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
    }

    function calculateTimelineContext() {
        if(this.knob.isWorking()) {
            if (this.currentGroove > HIGH_GROOVE_THRESHOLD) {
                this.timers.lowGrooveStart = null;
                calculatePositiveContext.call(this);
            } else if (this.currentGroove < LOW_GROOVE_THRESHOLD) {
                this.timers.highGrooveStart = null;
                calculateNegativeContext.call(this);
            }
        }
    }

    // liking it a lot
    function calculatePositiveContext() {
        if (!this.timers.highGrooveStart) {
            this.timers.highGrooveStart = this.currentFrameId;
            this.isShaking = true;
        } else if (this.timers.highGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.highGrooveStart = null;
            this.currentGroove = HIGH_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.isShaking = false;
        }
    }

    // hating it a lot
    function calculateNegativeContext() {
        if (!this.timers.lowGrooveStart) {
            this.timers.lowGrooveStart = this.currentFrameId;
            this.isShaking = true;
        } else if (this.timers.lowGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.lowGrooveStart = null;
            this.currentGroove = LOW_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.isShaking = false;
        }
    }

})(jQuery);

/*
        addEvents : function()
        {
            this._super();

            $(window).resize( $.proxy(this.onWindowResize, this) );
            this.config.joystick.ref.draggable({
                containment : "parent",
                axis        : "y",
                disabled    : false,
                start       : $.proxy(this.onGrooveStart, this),
                stop        : $.proxy(this.onGrooveStop, this),
                drag        : $.proxy(function(event, ui) { if(!this.isPlayingSong()) return false;}, this)
            });

            $('body>div').bind("dragstart", function(event, ui){
                  event.stopPropagation();
            });
        },

        getContextSizes : function()
        {
            this.config.canvas = {ref : this.config.container.ref.find("canvas")};
            if(this.config.canvas)
            {
                var ref = this.config.canvas.ref,
                    node = ref.get(0);

                this.config.sizes = {
                    top     : 0,
                    left    : 0,
                    height  : ref.height(),
                    width   : ref.width()
                };

                node.height = ref.height();
                node.width = ref.width();
                this.config.context = node.getContext("2d");
            }
        },

        addJoystick : function()
        {
            var joy = this.element.find(".joystick"),
                b = joy.find("b");

            this.joystickData = {
                containerRef    : joy,
                containerHeight : joy.innerHeight(),
                ref             : b,
                height          : b.outerHeight(),
                actualHeight    : 0,
                containerPosition : null
            };

            this.config.joystick.actualHeight = this.config.joystick.containerHeight - this.config.joystick.height;

            joy.css({
                "top" : (this.config.container.height/2) - (this.config.joystick.containerHeight / 2),
                "left": (this.config.container.width/2) - (joy.outerWidth() / 2)
            });
            this.config.joystick.containerPosition = joy.position();

            this.centerGrooveKnob();
        },

        centerGrooveKnob : function()
        {
            if(this.config.joystick)
            {
                this.config.joystick.ref.css("top", ((this.config.joystick.containerHeight / 2) - (this.config.joystick.height / 2)) + "px");
            }
        },

        animate : function()
        {
            if(this.data.animating)
            {
                if(!this.appHasFocus())
                {
                    this.focusLostError();
                }
                else
                {
                    if(this.frameShouldRender())
                    {
                        this.tick();
                        this.render();
                    }
                }


                var scope = this;
                requestAnimationFrame(function() { scope.animate(); });
            }
        },

        frameShouldSave : function()
        {
            return  !this.data.paused && (this.data.savetick + SAVE_FRAMERATE <= this.getNow());
        },

        render : function()
        {
            try {
                this.calculateGrooveCurve();
                this.displayFrequency();
                this.displayGroove();
                this.updateMultiplierUi();
                this.updateUiStatuses();
                this.addClasses();
                this.runGuiFx();

                this._super();
            } catch(e) {
                console.trace(e.message);
            }
        },

        displayGroove : function()
        {
            var groove = parseInt(this.data.groove*100, 10),
                context = this.config.context;

            if(groove > 99) groove = "∞";

            context.beginPath();
            context.arc(50, this.config.sizes.height - 50, 35, 0, 2 * Math.PI, false);
            context.fillStyle = 'rgba(250,250,250,.8)';
            context.fill();
            context.closePath();

            context.font = '20pt Tahoma';
            context.fillStyle = '#000';
            context.fillText(groove + "", 35, this.config.sizes.height - 35);

            if(!this.data.grooving)
            {
                var position = Math.abs(this.data.groove-1) * this.config.joystick.actualHeight;
                this.config.joystick.ref.css("top", position + "px");
            }
        },

        displayFrequency : function()
        {
            var frequency = this.data.frequency,
                i = 0,
                len = frequency.length,
                context = this.config.context,
                containerHeight = this.config.sizes.height,
                width = Math.ceil(this.config.sizes.width / len);

            context.clearRect (0, 0, this.config.sizes.width, this.config.sizes.height);

            for(var freq, color, height; i < len; i++)
            {
                freq = frequency[i];
                height = containerHeight * freq;
                color = parseInt(255 * freq, 10);

                if(height > containerHeight) height = containerHeight;
                else if(height < 1) height = 1;

                if(color > 250) color = 250;
                else if(color < 40) color = 40;

                context.beginPath();
                context.rect(i * width, containerHeight - height, width, height);
                context.fillStyle = 'rgb('+ color +','+ color +','+ color +')';
                context.fill();
            }
        },


        updateUiStatuses : function()
        {
            var container = this.config.container,
                context = this.config.context;

            if(this.data.multiplier !== 0)
            {
                context.beginPath();
                context.rect(20, 20, 50, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("X " + this.data.multiplier, 20, 50);
            }

            if(this.data.suckpowering)
            {
                context.beginPath();
                context.rect(20, 50, 150, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("Suckpowering!", 20, 75);
            }

            if(this.data.starpowering)
            {
                context.beginPath();
                context.rect(20, 50, 150, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("Starpowering!", 20, 75);
            }
        },

        addClasses : function()
        {
            var container = this.config.container;

            if(this.data.shaking !== container.shaking)
            {
                this.data.shaking ?
                    container.ref.addClass("shaking") :
                    container.ref.removeClass("shaking");
                container.shaking = this.data.shaking;
            }
            /*
            if(this.data.starpowering !== container.starpowering)
            {
                this.data.starpowering ?
                    container.ref.addClass("starpowering") :
                    container.ref.removeClass("starpowering");
                container.starpowering = this.data.starpowering;
            }

            if(this.data.suckpowering !== container.suckpowering)
            {
                this.data.suckpowering ?
                    container.ref.addClass("suckpowering") :
                    container.ref.removeClass("suckpowering");
                container.suckpowering = this.data.suckpowering;
            }

            if(this.data.multiplier !== container.multiplier)
            {
                container.ref.removeClass("multiplier_" + container.multiplier);
                container.ref.addClass("multiplier_" + this.data.multiplier);
                container.multiplier = this.data.multiplier;
            }     * /
            /*
            if(this.data.status !== container.status)
            {
                container.ref.removeClass("status_" + container.status);
                container.ref.addClass("status_" + this.data.status);
                container.status = this.data.status;
            }* /

            if(this.data.synchronising !== container.synchronising)
            {
                this.data.synchronising ?
                    container.ref.addClass("synchronising") :
                    container.ref.removeClass("synchronising");
                container.multiplier = this.data.multiplier;
            }

            /* handled in canvas now.
            if(this.data.timers.positiveMultiplierStart !== container.buildingPositiveMultiplier)
            {
                this.data.timers.positiveMultiplierStart ?
                    container.ref.addClass("multiplier-build-up") :
                    container.ref.removeClass("multiplier-build-up");
                container.buildingPositiveMultiplier = this.data.timers.positiveMultiplierStart;
            }

            if(this.data.timers.negativeMultiplierStart !== container.buildingNegativeMultiplier)
            {
                this.data.timers.negativeMultiplierStart ?
                    container.ref.addClass("multiplier-build-down") :
                    container.ref.removeClass("multiplier-build-down");
                container.buildingNegativeMultiplier = this.data.timers.negativeMultiplierStart;
            }       * /
        },

        runGuiFx : function()
        {
            this.shakeJoystick();
        },

        shakeJoystick : function()
        {
            if(!this.data.shaking && !this.config.joystick.moved) return;

            var originalTopValue = this.config.joystick.containerPosition.top,
                originalLeftValue = this.config.joystick.containerPosition.left,
                topValue = null,
                leftValue = null,
                random = 0;

            if(this.data.shaking)
            {
                random = Math.random();
                topValue = (random <= 0.5) ? originalTopValue + 2 : originalTopValue - 2;
                random = Math.random();
                leftValue = (random <= 0.5) ? originalLeftValue + 2 : originalLeftValue - 2;
                this.config.joystick.moved = true;
            }
            else if(this.config.joystick.moved)
            {
                this.config.joystick.moved = false;
                topValue = originalTopValue;
                leftValue = originalLeftValue;
            }

            this.config.joystick.containerRef.css({
                top : topValue + "px",
                left : leftValue + "px"
            });
        },

        updateMultiplierUi : function()
        {
            if(!this.data.timers.positiveMultiplierStart && !this.data.timers.negativeMultiplierStart)
                return;

            var pct = 0, deg, styles,
                context = this.config.context,
                level = Math.abs(this.data.multiplier);

            if(this.data.timers.positiveMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.positiveMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }

            else if(this.data.timers.negativeMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.negativeMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }

            if(level < 3)
            {
                // Draw the previous underlying stroke
                if(level > 0)
                {
                    context.beginPath();
                    context.arc(50, this.config.sizes.height - 50, 40, 0, 2 * Math.PI, false);
                    context.lineWidth = 12;
                    context.strokeStyle = 'rgba(0,0,0,' + (0.3 * level) + ')';
                    context.stroke();
                    context.closePath();
                }

                context.beginPath();
                context.arc(50, this.config.sizes.height - 50, 40, 0, (2*pct) * Math.PI, false);
                context.lineWidth = 12;
                context.strokeStyle = 'rgba(0,0,0,' + (0.3 * (level+1)) + ')';
                context.stroke();
                context.closePath();
            }
            else
            {
                context.beginPath();
                context.arc(50, this.config.sizes.height - 50, 40, 0, 2 * Math.PI, false);
                context.lineWidth = 12;
                context.strokeStyle = '#FF0000';
                context.stroke();
                context.closePath();
            }
        },

        toggleMultiplier : function(isPositive)
        {
            if(isPositive)
            {
                if(this.data.multiplier < MAX_MULTIPLIER)
                {
                    if(this.data.multiplier < -1) this.data.multiplier = 0;
                    this.data.multiplier++;
                    this.data.timers.activeMultiplierStart = this.data.frameId;
                    this.debug("data", "Multiplier positive applied", this.data.multiplier);
                }
            }
            else
            {
                if(this.data.multiplier > -MAX_MULTIPLIER)
                {
                    if(this.data.multiplier > 1) this.data.multiplier = 0;
                    this.data.multiplier--;
                    this.data.timers.activeMultiplierStart = this.data.frameId;
                    this.debug("data", "Multiplier negative applied", this.data.multiplier);
                }
            }
        },

        sendFramesPackage : function(idxStart, idxEnd)
        {
            this.data.synchronising = true;

            $.ajax(this.config.tmtUrl, {
                type : "POST",
                data: { frames : this.data.reviewFrames.slice(idxStart, idxEnd) },
                success : $.proxy(this.onSyncSuccess, this),
                error : $.proxy(this.onSyncFail, this)
            });
        },

        appHasFocus : function()
        {
            return this.data.appHasFocus === true;
        },

        focusLostError : function()
        {
            if(this.isPlayingSong())
            {
                var scope = this,
                    container = this.config.container;

                if(!this.config.container.isFocusLost)
                {
                    var btn = container.ref.find(".focus-lost button"),
                        onclick = function() {
                            container.ref.removeClass("focuslost");
                            container.isFocusLost = false;
                            btn.unbind("click");
                            scope.resume();
                        };

                    this.pause();
                    this.config.container.isFocusLost = true;
                    btn.click(onclick);
                    container.ref.addClass("focuslost");
                    btn.focus();
                }
            }
        },

        // LOOPS - The Math
        // Tick contains the calulations that have no direct visual impact
        tick : function()
        {
            try {
                this.logFrame();
                this.checkGrooveIntensity();
                this.checkStarpowers();
                this.checkMultiplier();
                this.checkMultiplierTimers();
                this.decayGrooveCurve();
                this.saveCurrentFrame();
                this.saveEquilizerFrame();
            } catch(e) {
                console.error(e);
                console.trace(e.message);
            }
        },

        logFrame : function()
        {
            this.data.frameId++;
            this.data.tick = this.getNow();
        },

        saveCurrentFrame : function()
        {
            if(this.frameShouldSave())
            {
                var lastFrame = null,
                    idx = null
                    currentFrame = {
                        gv  : this.data.groove,
                        st  : this.data.starpowering === true ? 1 : 0,
                        su  : this.data.suckpowering === true ? 1 : 0,
                        m   : this.data.multiplier,
                        p   : this.data.position,
                        o   : 0
                   };

                if(this.data.reviewFrames.length > 0)
                {
                    lastFrame = this.data.reviewFrames[this.data.reviewFrames.length - 1];
                }

                if(lastFrame && (lastFrame["p"] != currentFrame["p"] || lastFrame["gv"] != currentFrame["gv"]) )
                {
                    this.data.reviewFrames.push(currentFrame);
                    this.data.savetick = this.getNow();
                }
                else if(!lastFrame)
                {
                    this.data.reviewFrames.push(currentFrame);
                    this.data.savetick = this.getNow();
                }
            }

            if(this.data.frameId % FRAMES_PER_SAVE === 0)
            {
                this.sendFramesPackage(this.data.lastframeSent, this.data.reviewFrames.length);
                this.data.lastframeSent = this.data.reviewFrames.length - 1;
            }
        },

        calculateGrooveCurve : function()
        {
            if(this.config.joystick)
            {
                var value       = parseFloat(this.config.joystick.ref.css("top")),
                    height      = this.config.joystick.actualHeight,
                    pctOfHeight = value / height,
                    computedValue = (1 - pctOfHeight) + this.data.decay,
                    centerRange = GROOVE_DECAY_VALUE * 2;

                this.data.groove = computedValue;

                if      (this.data.groove < 0)  this.data.groove = 0;
                else if (this.data.groove > 1)  this.data.groove = 1;
                else if (this.data.groove > (MIDDLE_GROOVE_VALUE - centerRange) && this.data.groove < (MIDDLE_GROOVE_VALUE + centerRange))
                                                this.data.groove = 0.5;
            }
        },

        checkStarpowers : function()
        {
            if(this.data.starpowering)
            {
                if(this.data.timers.starpowerStart + FRAMES_PER_POWERING <= this.data.frameId)
                {
                    this.data.timers.starpowerStart = null;
                    this.data.starpowering = false;
                }
            }

            if(this.data.suckpowering)
            {
                if(this.data.timers.suckpowerStart + FRAMES_PER_POWERING <= this.data.frameId)
                {
                    this.data.timers.suckpowerStart = null;
                    this.data.suckpowering = false;
                }
            }
        },

        checkGrooveIntensity : function()
        {
            if(this.data.grooving)
            {
                // liking it a lot
                if(this.data.groove >= HIGH_GROOVE_THRESHOLD && !this.data.starpowering)
                {
                    this.data.timers.lowGrooveStart = null;

                    if(!this.data.timers.highGrooveStart)
                    {
                        this.data.timers.highGrooveStart = this.data.frameId;
                    }
                    else if(this.data.timers.highGrooveStart + FRAME_PER_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = false;
                        this.data.timers.highGrooveStart = null;
                        this.data.starpowering = true;
                        this.data.timers.starpowerStart = this.data.frameId;
                    }
                    else if(this.data.timers.highGrooveStart + FRAMES_TO_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = true;
                    }
                    return;
                }
                // hating it a lot
                else if(this.data.groove <= LOW_GROOVE_THRESHOLD && !this.data.suckpowering)
                {
                    this.data.timers.highGrooveStart = null;
                    if(!this.data.timers.lowGrooveStart)
                    {
                        this.data.timers.lowGrooveStart = this.data.frameId;
                    }
                    else if(this.data.timers.lowGrooveStart + FRAME_PER_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = false;
                        this.data.suckpowering = true;
                        this.data.timers.lowGrooveStart = null;
                        this.data.timers.suckpowerStart = this.data.frameId;
                    }
                    else if(this.data.timers.lowGrooveStart + FRAMES_TO_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = true;
                    }
                    return;
                }
            }

            // Reset if the groove is in between
            this.data.shaking = false;
            this.data.timers.lowGrooveStart = null;
            this.data.timers.highGrooveStart = null;
        },

        /**
         * Checks the global mutiplier timer to set or remove the multiplier
         * flag. Also triggers
         * /
        checkMultiplier : function()
        {
            // if the timer is triggered, user is multiplying from liking a long time
            if(this.data.timers.activeMultiplierStart)
            {
                // Cancel out the multiplier timer, animation is complete
                if(this.data.timers.activeMultiplierStart + FRAMES_PER_MULTIPLY <= this.data.frameId)
                {
                    this.data.multiplier = 0;
                    this.data.timers.activeMultiplierStart = null;
                }
            }
            // if no timer is set, but we are powering start it right away
            else if(this.data.starpowering)
            {
                this.toggleMultiplier(true);
            }
            else if(this.data.suckpowering)
            {
                this.toggleMultiplier(false);
            }
        },

        checkMultiplierTimers : function()
        {
            // If user is not actively pressing on the knob, no
            // multiplier can become active;
            if(this.data.grooving)
            {
                if(this.data.groove >= HIGH_GROOVE_MULTIPLIER_THRESHOLD)
                {
                    // When there is enjoyment, cancel out any negative
                    // multiplers
                    this.data.timers.negativeMultiplierStart = null;
                    if(this.data.multiplier < 0) this.data.multiplier = 0;

                    if(!this.data.timers.positiveMultiplierStart)
                    {
                        this.data.timers.positiveMultiplierStart = this.data.frameId;
                    }
                    else if(this.data.timers.positiveMultiplierStart + FRAMES_PER_MULTIPLIER <= this.data.frameId)
                    {
                        this.toggleMultiplier(true);
                        this.data.timers.positiveMultiplierStart = null;
                    }
                    return;
                }
                else if(this.data.groove <= LOW_GROOVE_MULTIPLIER_THRESHOLD)
                {
                    // When there isnt enjoyment, cancel out any positive
                    // multiplers
                    this.data.timers.positiveMultiplierStart = null;
                    if(this.data.multiplier > 0) this.data.multiplier = 0;

                    if(!this.data.timers.negativeMultiplierStart)
                    {
                        this.data.timers.negativeMultiplierStart = this.data.frameId;
                    }
                    else if(this.data.timers.negativeMultiplierStart + FRAMES_PER_MULTIPLIER <= this.data.frameId)
                    {
                        this.toggleMultiplier(false);
                        this.data.timers.negativeMultiplierStart = null;
                    }
                    return;
                }
            }

            this.data.timers.positiveMultiplierStart = null;
            this.data.timers.negativeMultiplierStart = null;
        },

        decayGrooveCurve : function()
        {
            if(this.config.joystick)
            {
                this.data.decay = 0;
                if(!this.data.grooving && !this.data.starpowering && !this.data.suckpowering)
                {
                    if      (this.data.groove > MIDDLE_GROOVE_VALUE) this.data.decay = -GROOVE_DECAY_VALUE;
                    else if (this.data.groove < MIDDLE_GROOVE_VALUE) this.data.decay = GROOVE_DECAY_VALUE;
                }
            }
        },

        onSongEnd : function()
        {
            this._super();

            this.data.songIsOver = true;
            this.config.container.ref.addClass("loading");

            var last = this.data.reviewFrames[this.data.reviewFrames.length - 1];
            if(this.data.lastframeSent < this.data.reviewFrames.length)
            {
                last["o"] = 1;
            }
            // Make sure we send in at least a frame.
            else
            {
                last["o"] = 1;
                this.data.reviewFrames.push(last);
            }

            this.sendFramesPackage(this.data.lastframeSent, this.data.reviewFrames.length);
        },

        onGrooveStart : function()
        {
            this.data.grooving = true;
        },

        onGrooveStop : function()
        {
            this.data.grooving = false;
        },

        onSyncSuccess : function()
        {
            this.data.synchronising = false;

            if(this.data.songIsOver)
            {
                this.config.container.ref.removeClass("loading");
                this.config.container.ref.addClass("completed");
            }
        },

        onSyncFail : function()
        {
            this.data.synchronising = false;
        },

        isPlayingSong : function()
        {
            return this.data.status === "playing";
        },

        onWindowResize : function()
        {
            //this.setDisplayHeight();
        },

        onWindowVisibility : function(isVisible)
        {
            this.data.appHasFocus = isVisible;
            this.debug("data", "appHasFocus", this.data.appHasFocus);
        }*/


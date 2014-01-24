(function() {
    var tmt = window.tmt = {};

    var GROOVE_DECAY_VALUE  = 0.0005,
        MIDDLE_GROOVE_VALUE = 0.500,
        FRAMERATE           = 24,
        APP_FRAMERATE       = 1 / FRAMERATE * 1000,  //  * 1k because js parses miliseconds
        //FRAMES_PER_SAVE     = FRAMERATE * 15, // every 15 seconds 
        FRAMES_PER_SAVE     = FRAMERATE*5, // expects that it doesn't take 5 seconds to save, though it would still work
        MAX_MULTIPLIER      = 2,
  
        // These define the length of all 'animations'
        DURATION_COMPARE    = 3.5 * 60, // the basis for all effects is based on 1 sec when song is 3m30s
        LENGTH_TO_SHAKE     = 0.1,
        LENGTH_POWERING     = 4.2,
        LENGTH_SHAKING      = 1.8,
        LENGTH_TO_MULTIPLIER = 3,
        LENGTH_MULTIPLYING  = 3.1,
        
        FRAMES_PER_POWERING = null,
        FRAMES_TO_SHAKE     = null,
        FRAME_PER_SHAKE     = null,
        FRAMES_PER_MULTIPLIER  = null,
        FRAMES_PER_MULTIPLY = null,       
        
        HIGH_GROOVE_MULTIPLIER_THRESHOLD = .75,
        LOW_GROOVE_MULTIPLIER_THRESHOLD = .25,
        HIGH_GROOVE_THRESHOLD = .98,
        LOW_GROOVE_THRESHOLD = .02; 

    var player = tmt.player = function(config) {
        this.config = config;
        
        // Make constant available to overriding classes
        this.config.APP_FRAMERATE = APP_FRAMERATE;
        
        this.data = {
            timers : {},
            frequency : [], 
            groove : MIDDLE_GROOVE_VALUE,
            decay: 0,
            tick:0,
            frameId : 0,
            multiplier:0,
            reviewFrames : [],
            appHasFocus : true
        };        
    };
                
    player.prototype = {
        
        debug : function(type, variable, value)
        {
            if(this.config.debug)
            {
                console.log("["+type+"] " + variable + ": " + value);
            }
        },
        
        init : function()
        {
            this.config.container   = {ref : $("#player")};            
            this.config.seek        = {ref : $("#controls .seek")};            
            this.config.trackinfo   = {ref : $("#controls .trackinfo")};            
            this.config.body        = {ref : $("body")};            
            this.config.groove      = {ref : $("#player .groove")};
            
            this.debug("config", "trackDuration", this.config.trackDuration);
            
            this.customizeOptionsBySong();            
            
            this.addFrequency();
            this.setDisplaySize();            
            this.addJoystick();               
            this.addMultiplierBar();      
            this.addEvents();            
        
            this.loadSongStreamer();
        },
        
        customizeOptionsBySong : function()
        {
            FRAMES_PER_POWERING = FRAMERATE * (this.config.trackDuration * LENGTH_POWERING / DURATION_COMPARE);
            FRAMES_TO_SHAKE     = FRAMERATE * LENGTH_TO_SHAKE;
            FRAME_PER_SHAKE     = FRAMERATE * LENGTH_SHAKING;
            FRAMES_PER_MULTIPLIER = FRAMES_PER_POWERING * (this.config.trackDuration * LENGTH_TO_MULTIPLIER / DURATION_COMPARE); 
            FRAMES_PER_MULTIPLY = FRAMES_PER_MULTIPLIER * (this.config.trackDuration * LENGTH_MULTIPLYING / DURATION_COMPARE);
            
            this.debug("config", "FRAMES_PER_POWERING", FRAMES_PER_POWERING);
            this.debug("config", "FRAMES_TO_SHAKE", FRAMES_TO_SHAKE);
            this.debug("config", "FRAME_PER_SHAKE", FRAME_PER_SHAKE);
            this.debug("config", "FRAMES_PER_MULTIPLIER", FRAMES_PER_MULTIPLIER);
            this.debug("config", "FRAMES_PER_MULTIPLY", FRAMES_PER_MULTIPLY);
        },

        
        // TOOLS
        // -----
            
        getNow : function()
        {
            return Date.now();
        },  
        
        centerGrooveKnob : function()
        {
            if(this.config.joystick)
            {
                this.config.joystick.ref.css("top", ((this.config.joystick.containerHeight / 2) - (this.config.joystick.height / 2)) + "px");
            }
        },
                
        frameShouldRender : function()
        {            
            return  !this.paused &&
                    (this.data.tick + APP_FRAMERATE <= this.getNow());
        },                    
               
        // PLAYER CONTROLS                  
                                
        play : function()
        {               
            if(this.isPlayingSong())
            {
                return this.pause();
            }
            else if(this.paused)
            {
                return this.resume();
            }
                        
            this.apicontrols_play();
            this.startAnimating();
            this.setupDisplay();
        },
                      
        stop : function()
        {
            this.apicontrols_stop();            
            this.stopAnimating();
            this.resetDisplay();
        },
                
        pause : function()
        {
            this.paused = true;
            this.apicontrols_pause(); 
        }, 
                
        resume : function()
        {
            this.paused = false;
            this.apicontrols_resume(); 
        },        
        
        startAnimating : function()
        {
            this.animating = true;
            this.animate();
        },
                
        stopAnimating : function()
        {
            this.animating = false;
            this.data.shaking = false;
            this.data.starpowering = false;            
            this.data.suckpowering = false;
            
            this.data.groove = MIDDLE_GROOVE_VALUE;
            this.data.decay = 0;
            this.data.reviewFrames = [];
            
            this.data.playingTrack = null;
            this.data.playingSource = null;        
            this.data.position = 0;            
        },                
                        
                
        // VISUALS
        // -------
                
        setDisplaySize : function()
        {
            var boxHeight = $("body").height() - $("#controls").outerHeight() - $("header").outerHeight();
            
            this.config.container.height = boxHeight;
            this.config.container.width = this.config.container.ref.width();
            this.config.container.ref.css("height", boxHeight + "px");             
            this.config.container.ref.find(".frequency").css({"line-height" : boxHeight + "px"});
        },
                
        addFrequency : function()
        {
            var i   = 0,
                max = 31,
                str = "";
        
            for(; i < max; i++) str += "<b></b>";
            
            this.config.container.ref.append('<div class="frequency">' + str + '</div>');
            
            var freq = $(".frequency");
            this.config.bars = {
                ref             : freq.find("b"),
                containerRef    : freq
            };
        },
                
        addJoystick : function()
        {            
            this.config.container.ref.append('<div class="joystick"><b></b></div>');
            
            var joy = $( ".joystick" ),
                b = joy.find("b");
        
            this.config.joystick = {
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
        
        addMultiplierBar : function()
        {
            this.config.container.ref.append('<div class="multiplier-progress"></div>');
            this.config.multiplierBar = { ref: $('.multiplier-progress') };
        },
                
        setupDisplay : function()
        {
            if(this.config.bars)
            {
                this.config.bars.ref.css({ "height" : "1%", "backgroundColor" : "#ccc" });             
            }
            
            if(this.config.joystick)
            {
                this.config.joystick.ref.draggable("enable");
                this.centerGrooveKnob();
            }
        },        
        
        // LOOPS - Visual
               
        animate : function()
        {   
            if(this.animating)
            {
                if(!this.appHasFocus())
                {
                    this.focusLostError();
                }
                else if(this.frameShouldRender())
                {
                    this.tick();
                    this.render();       
                }
            
                var scope = this; 
                requestAnimationFrame(function() { scope.animate(); });
            }
        },                
         
        // Render only handles things that have visual impact
        render : function()
        {
            try
            {
                if(this.config.fakeFrequency) this.setFakeFrequency();
                this.calculateGrooveCurve();
                this.displayGroove();                
                this.displayFrequency(); 
                this.displaySongInfo();    
                this.addClasses();                 
                this.runGuiFx();            
            }
            catch (e) { console.log(e.message); }
        },
        
        displayGroove : function()
        {
            var groove = parseInt(this.data.groove*100, 10);            
            if(groove > 98) groove = "∞";
            this.config.groove.ref.html(groove);
               
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
                bars = this.config.bars.ref,
                bar, freq, color, height;
        
            for(; i < len; i++)
            {
                freq = frequency[i];
                height = 99.9 * freq;            
                bar = $(bars.get(i));
                color = parseInt(255 * freq, 10);
                
                if(height > 100) height = 100;
                else if(height < 1) height = 1;
                
                if(color > 250) color = 250;
                else if(color < 40) color = 40;
                
                bar.css({
                   "height" : height + "%",
                   "backgroundColor" : 'rgb('+ color +','+ color +','+ color +')'
                });
            }
        },        
                             
        displaySongInfo : function()
        {
            var playingTrack = this.data.playingTrack,
                playingSource = this.data.playingSource;
        
            if(this.config.trackinfo.requiresAlbumRepaint)
            {                
                this.config.trackinfo.ref.find(".artist").text(playingSource.artist);
                this.config.trackinfo.ref.find(".album").text(playingSource.name);
                this.config.trackinfo.ref.find(".image").html('<img src="' + playingSource.bigIcon + '"/>');
                this.config.trackinfo.requiresAlbumRepaint = false;
            }
            
            if(this.config.trackinfo.requiresTrackRepaint)
            {
                this.config.trackinfo.ref.find(".title").text(playingTrack.name);
                this.config.trackinfo.requiresTrackRepaint = false;
            }
            
            if(playingTrack)
            {
                this.config.seek.ref.find(".progress").css("width", ((this.data.position  / playingTrack.duration) * 100) + "%");
            }
        },
                
        runGuiFx : function()
        {
            this.shakeJoystick();            
            this.updateMultiplierUi();
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
            
            var pct = 0, deg, styles;
            
            if(this.data.timers.positiveMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.positiveMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }
            
            else if(this.data.timers.negativeMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.negativeMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }                        
                        
            deg = 360*pct;
            styles = "-moz-transform:rotate("+deg+"deg); -webkit-transform:rotate("+deg+"deg); -o-transform:rotate("+deg+"deg); transform:rotate("+deg+"deg)";
            
            this.config.multiplierBar.ref.html('<div class="percent"></div><div id="slice"'+(pct > 50?' class="gt50"':'')+'><div class="pie" style="'+styles+'"></div>'+(pct > 50?'<div class="pie fill" style="'+styles+'"></div>':'')+'</div>');            
        },
        
        
        // LOOPS - The Math        
        // Tick contains the calulations that have no direct visual impact
        tick : function()
        {            
            try
            {
                this.logFrame();
                this.checkGrooveIntensity();    
                this.checkStarpowers();   
                this.checkMultiplier();   
                this.checkMultiplierTimers();   
                this.decayGrooveCurve();  
                this.saveCurrentFrame();              
            }
            catch (e) { console.log(e.message); }
        },
                    
        logFrame : function()
        {            
            this.data.frameId++;
            this.data.tick = this.getNow();
        },
             
        saveCurrentFrame : function()
        {            
            var length = this.data.reviewFrames.length,
                isDone = this.data.position >= this.config.trackDuration,
                currentFrame = {
                    gv  : this.data.groove,
                    st  : this.data.starpowering === true ? 1 : 0,
                    su  : this.data.suckpowering === true ? 1 : 0,
                    g   : this.data.grooving  === true ? 1 : 0,
                    m   : this.data.multiplier,
                    p   : this.data.position,
                    o   : isDone ? 1 : null
                };
                                   
            this.data.reviewFrames.push(currentFrame);

            if(isDone || (length > 0 && (length+1) % FRAMES_PER_SAVE === 0))
            {
                this.sendFramesPackage((length+1) - FRAMES_PER_SAVE, length);
            }    
            
            /* This might be a bit too conservative 
            if(prevFrame)
            {           
                if(currentFrame["p"] !== prevFrame["p"])
                {
                    var keys = ["st", "su", "gv", "g"];
                    for(var attr in keys)
                    {
                        if(currentFrame[keys[attr]] !== prevFrame[keys[attr]])
                        {
                            shouldSave = true;
                            break;
                        }
                    }
                }
            }
            
            if(!prevFrame || shouldSave)
            {
                this.data.reviewFrames.push(currentFrame);
                
                if(length > 0 && (length+1) % FRAMES_PER_SAVE === 0)
                {
                    this.sendFramesPackage((length+1) - FRAMES_PER_SAVE, length);
                }                
            }*/
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
         */
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
               
        setFakeFrequency : function()
        {
            var fakeFreq = [];
            for(var i = 0, len = 25; i < len; i++) fakeFreq[i] = Math.random();        
            this.onFrequencyChange(fakeFreq);   
        },       
                       
        resetDisplay : function()
        {
            if(this.config.bars)
            {
                this.config.bars.ref.css({ "height" : "1%", "backgroundColor" : "#ccc" });             
            }
            
            if(this.config.joystick)
            {
                this.config.joystick.ref.draggable("disable");
                this.centerGrooveKnob();
            }
            
            if(this.config.seek) 
            {
                this.config.seek.ref.find(".progress").css("width", 0);
            }
            
            this.config.trackinfo.ref.find(".artist").text("");
            this.config.trackinfo.ref.find(".album").text("");
            this.config.trackinfo.ref.find(".title").text("");  
            this.config.trackinfo.ref.find(".image").html("");  
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
            }     
            
            if(this.data.status !== container.status)
            {
                container.ref.removeClass("status_" + container.status);
                container.ref.addClass("status_" + this.data.status);
                container.status = this.data.status;
            }
                        
            if(this.data.synchronising !== container.synchronising)
            {
                this.data.synchronising ? 
                    container.ref.addClass("synchronising") :
                    container.ref.removeClass("synchronising");            
                container.multiplier = this.data.multiplier;
            }               
            
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
                        
        
        // EVENTS
        // ------
        
        addEvents : function()
        {
            var scope = this;
            $(window).resize( $.proxy(this.onWindowResize, this) );                                      
            
            // set up the controls
            $('#play').click( $.proxy(this.play, this) );
            $('#stop').click( $.proxy(this.stop, this) );
            
            $( ".joystick b" ).draggable({ 
                containment : "parent", 
                axis        : "y", 
                disabled    : true,
                start       : function() { scope.onGrooveStart(); },
                stop        : function() { scope.onGrooveStop(); },
                drag        : function(event, ui) { if(!scope.isPlayingSong()) return false;}
            });

            $('body>div').bind("dragstart", function(event, ui){
                  event.stopPropagation();
            });
        },      
                 
        appHasFocus : function()
        {
            return this.data.appHasFocus;            
        },
               
        focusLostError : function()
        {
            if(this.isPlayingSong())
            {
                var scope = this,
                    body = this.config.body.ref,
                    btn = $("#focusLost button"),
                    onclick = function() {
                        body.removeClass("focuslost"); 
                        btn.unbind("click");
                        scope.resume();
                    };
                
                this.pause();
                btn.click(onclick);       
                body.addClass("focuslost");
            }
        },      
                
        onFrequencyChange : function(data)
        {
            for(var i = 0, len = data.length; i < len; i++) this.data.frequency[i] = parseFloat(data[i]);
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
        },
                
        onSyncFail : function()
        {
            this.data.synchronising = false;
        },
                
        onStatusChange : function (status)
        {
            var prevStatus = this.data.status;
            this.debug("data", "onStatusChange", status);
            this.data.status = status;
            
            // When buffering, do not calculate the curves.
            if(status.match(/buffering/))
            {
                this.pause();
            }
            // When returning form buffering, resume previous pause
            else if(status.match(/playing/) && prevStatus.match(/buffering/))
            {
                this.resume();
            }
        },
                
        isPlayingSong : function()
        {
            return this.data.status === "playing";
        },
                
        onTrackChange : function(playingTrack)
        {
            this.debug("data", "onTrackChange", playingTrack);
            this.data.playingTrack = playingTrack;            
            this.config.trackinfo.requiresTrackRepaint = true;
        },
                
        onPlayingSourceChanged : function(playingSource)
        {
            this.debug("data", "onPlayingSourceChanged", playingSource);
            this.data.playingSource = playingSource;
            this.config.trackinfo.requiresAlbumRepaint = true;
        },
                
        onUserChange : function(user)
        {
            this.debug("data", "onUserChange", user);
            this.data.user = user;
        },
                
        onPositionChange  : function(position)
        {
            this.data.position = position;
        },
                
        onWindowResize : function()
        {
            this.setDisplayHeight();
        },
        
        onWindowVisibility : function(isVisible)
        {
            this.data.appHasFocus = isVisible;
            this.debug("data", "appHasFocus", this.data.appHasFocus);
        },               
                
        // These must be overriden :
        setupCallback : function()
        {
            console.log("Warning : Setup callback called, but no player api loaded.");
        },
                
        apicontrols_play    : function() { console.log("Warning : Play event called, but no player api loaded."); },
        apicontrols_stop    : function() { console.log("Warning : Stop event called, but no player api loaded."); },
        apicontrols_pause   : function() { console.log("Warning : Pause event called, but no player api loaded."); },
        apicontrols_resume  : function() { console.log("Warning : Resume event called, but no player api loaded."); },        
        loadSongStreamer    : function() { console.log("Warning : Resume event called, but no player api loaded."); },
        onReady             : function() { console.log("Warning : Resume event called, but no player api loaded."); }
    };
    
})();
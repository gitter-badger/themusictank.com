(function() {
    
    var FRAMERATE           = 24,
        APP_FRAMERATE       = 1 / FRAMERATE * 1000,
        FRAMES_PER_SAVE     = FRAMERATE * 5;

    var Player = Class.extend({
                 
        init : function(config)
        {
            this.config = config;
        
            // Make constant available to overriding classes
            this.config.APP_FRAMERATE = APP_FRAMERATE;
            this.config.container   = {ref : $(this.config.containerSelector)};   
            this.config.seek        = {ref : this.config.container.ref.find(".seek")};               
            this.config.seekCursor  = {ref : this.config.container.ref.find(".cursor")};  
            this.config.progress    = {ref : this.config.seek.ref.find(".progress")};        
            this.config.playBtn     = {ref : this.config.container.ref.find('button[name=play]')};
            this.config.timer       = {ref : $(this.config.containerSelector + ".timed .time")};
            
            this.data = {
                frequency : [],
                status : null,
                tick:0,
                frameId : 0,
                wavelength : []
            };
            
            return this;
        },
        
        run : function()
        {
            
            this.addEvents();  
            this.loadSongStreamer();   
        },
                
        addEvents : function()
        {
            // set up the controls
            if(this.config.playBtn)
            {
                this.config.playBtn.ref.click( $.proxy(this.play, this) );
            }
        },
        
        getNow : function()
        {
            return Date.now();
        },  
        
        play : function()
        {
            if(this.isPlayingSong())
            {
                return this.pause();
            }
            else if(this.data.paused)
            {
                return this.resume();
            }
             
            try {
                this.startAnimating();
                this.apicontrols_play();
            }
            catch(e) { }
        },
                      
        stop : function()
        {
            this.apicontrols_stop();         
            this.stopAnimating();
        },
                
        pause : function()
        {
            this.data.paused = true;
            this.apicontrols_pause(); 
        }, 
                
        resume : function()
        {
            this.data.paused = false;
            this.apicontrols_resume(); 
        },      
                       
        startAnimating : function()
        {
            this.data.animating = true;
            this.animate();
        },
                
        stopAnimating : function()
        {
            this.data.animating = false;
            this.data.position = 0;            
        },  
        
        animate : function()
        {   
            if(this.data.animating)
            {
                if(this.frameShouldRender())
                {
                    this.tick();
                    this.render();       
                }
            
                var scope = this; 
                requestAnimationFrame(function() { scope.animate(); });
            }
        },      
                
        frameShouldRender : function()
        {            
            return  !this.data.paused &&
                    (this.data.tick + APP_FRAMERATE <= this.getNow());
        },                 
         
        // Render only handles things that have visual impact
        render : function()
        {
            try
            {
                this.displaySongInfo();
                this.displaySeekBar();
            }
            catch (e) { console.log(e.message); }
        },
        
        tick : function()
        {            
            try
            {
                this.logFrame();  
                this.saveEquilizerFrame();
            }
            catch (e) { console.log(e.message); }
        },
                    
        logFrame : function()
        {            
            this.data.frameId++;
            this.data.tick = this.getNow();
        },
        
        saveEquilizerFrame : function()
        {
            if(this.config.saveEquilizer)
            {
                for(var total = 0, i = 0, len = this.data.frequency.length; i < len; i++) 
                {
                    total += this.data.frequency[i];
                }
                
                this.data.wavelength[Math.floor(this.data.position)] = parseFloat((total / len));
            }
        },
        
        sendEquilizerPackage : function()
        {
            this.data.synchronising = true;
            
            $.ajax(this.config.equilizeUrl, {
                type : "POST",                
                data: { waves : this.data.wavelength },
                success : $.proxy(this.onSyncSuccess, this),
                error : $.proxy(this.onSyncFail, this)
            });            
        },
        
        displaySongInfo : function()
        {
            if(this.config.trackDuration && this.config.progress.position !== this.data.position)
            {
                var pct = ((this.data.position  / this.config.trackDuration) * 100);
                
                if(pct > 99)    pct = 100;
                
                this.config.progress.ref.css("width", pct + "%");
                this.config.progress.position = this.data.position;
            }
            
            if(this.config.timer && this.config.timer.position !== this.data.position)
            {
                var position = this.data.position;
                var total = this.config.trackDuration;
                
                var posMins = Math.floor(position / 60),
                    posSecs =  Math.floor(position - (posMins * 60)),
                    totMins = Math.floor(total / 60),
                    totSecs =  Math.floor(total - (totMins * 60));
            
                if(posSecs < 10) posSecs = "0" + posSecs;
                if(totSecs < 10) totSecs = "0" + totSecs;
                
                this.config.timer.ref.html(posMins + ":" + posSecs + " / " + totMins + ":" + totSecs);
                this.config.timer.position = this.data.position;
            }
        },
        
        displaySeekBar : function()
        {
            if(this.config.seekCursor)
            {
                var pct = ((this.data.position  / this.config.trackDuration) * 100);
                                
                this.config.seekCursor.ref.css("left", pct + "%");
            }
        },
        
        isPlayingSong : function()
        {
            return this.data.status === "playing";
        },                
               
        linkGraph : function(graph)
        {
            this.config.graph = graph;
            graph.config.container = {ref: this.config.container.ref.find("canvas")};
        },
        
        updatePlayButtonLabel : function()
        {
            if(this.config.playBtn)
            {
                this.config.playBtn.ref.html(this.data.status);
            }
        },
        
        onStatusChange : function (status)
        {
            var prevStatus = this.data.status;
            this.data.status = status;
            
            this.updatePlayButtonLabel();
            
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
        
        // Saving wavelength + bars
        onFrequencyChange : function(data)
        {
            this.data.frequency = [];
            var total = 0, i = 0, len = data.length, value = 0;
            for( ; i < len; i++) 
            {
                value = parseFloat(data[i]);
                total += value;
                this.data.frequency.push(value);
            }
        },
        
        onPositionChange  : function(position)
        {
            this.data.position = position;
        },           
        
        onSongEnd : function()
        {
            if(this.config.saveEquilizer)
            {
                this.sendEquilizerPackage();
                this.config.saveEquilizer = false;
            }
        },        
                
        // These must be overriden :
        setupCallback       : function(){return this;},
        apicontrols_play    : _notOverriden,
        apicontrols_stop    : _notOverriden,
        apicontrols_pause   : _notOverriden,
        apicontrols_resume  : _notOverriden,
        loadSongStreamer    : _notOverriden,
        onReady             : _notOverriden,
        onTrackChanged      : _notOverriden,
        onUserChange        : _notOverriden
    });

    function _notOverriden() {};
    
    tmt.Player = Player;
    
})();
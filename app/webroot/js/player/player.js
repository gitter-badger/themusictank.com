(function() {
    var tmt = window.tmt = {};

    var FRAMERATE           = 24,
        APP_FRAMERATE       = 1 / FRAMERATE * 1000;    

    var player = tmt.player = function(config)
    {
        this.config = config;
    };

    player.prototype = {
                  
        init : function()
        {
            // Make constant available to overriding classes
            this.config.APP_FRAMERATE = APP_FRAMERATE;
            this.config.container   = {ref : $(this.config.containerSelector)};   
            this.config.seek        = {ref : this.config.container.ref.find(".seek")};               
            this.config.seekCursor      = {ref : this.config.container.ref.find(".cursor")};  
            this.config.progress    = {ref : this.config.seek.ref.find(".progress")};        
            
            this.config.saveEquilizer = !(this.config.equilizerData);
            
            this.data = {
                frequency : [],
                status : null,
                tick:0,
                frameId : 0,
                wavelength : []
            };        
            
            this.addEvents();  
            this.loadSongStreamer();            
        },
                
        addEvents : function()
        {
            // set up the controls
            this.config.container.ref.find('button[name=play]').click( $.proxy(this.play, this) );
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
                this.apicontrols_play();
                this.startAnimating();
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
            if(this.config.saveEquilizer && this.data.frameId % FRAMERATE === 0)
            {
                for(var total = 0, i = 0, len = this.data.frequency.length; i < len; i++) 
                {
                    total += this.data.frequency[i];
                }
                
                // a cause que jai pas la confirmation que le ui update, il faudrai associer le frame a la position
                // dans larray. et ajouter when is null/ save
                this.data.wavelength.push(total / len);
            }
        },
        
        displaySongInfo : function()
        {
            if(this.config.trackDuration)
            {
                var pct = ((this.data.position  / this.config.trackDuration) * 100);
                
                if(pct > 99)    pct = 100;
                else if(pct < 1)pct = 1;
                
                this.config.progress.ref.css("width", pct + "%");
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
        
        onStatusChange : function (status)
        {
            var prevStatus = this.data.status;
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
        
        // Saving wavelength + bars
        onFrequencyChange : function(data)
        {
            var total = 0, i = 0, len = data.length, value = 0;
            for( ; i < len; i++) 
            {
                value = parseFloat(data[i]);
                total += value;
                this.data.frequency[i] = value;
            }
        },
        
        onPositionChange  : function(position)
        {
            this.data.position = position;
        },           
        
        onUserChange : function() {
            
        },
        
                
        // These must be overriden :
        setupCallback       : _notOverriden,
        apicontrols_play    : _notOverriden,
        apicontrols_stop    : _notOverriden,
        apicontrols_pause   : _notOverriden,
        apicontrols_resume  : _notOverriden,
        loadSongStreamer    : _notOverriden,
        onReady             : _notOverriden,
        onTrackChanged      : _notOverriden
    };

    function _notOverriden() {
        
    };
    
})();
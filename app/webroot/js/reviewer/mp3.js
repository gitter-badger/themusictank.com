(function() {


    var player = window.tmt.player.prototype,
        TRACK_LENGTH_DIFF = 5;

    player.loadSongStreamer = function()
    {
        if(_hasAPISupport() && _hasSongAPISupport())
        {
            this.onReady();
        }
        else
        {
            this.config.body.addClass("featureunavailable");
        }
    };
    
    player.onReady = function()
    { 
        this.config.body.ref.removeClass("loading");
        this.config.body.ref.addClass("askingfordrop");
               
        var drop = $("#drop"),
            inputRef = $("#fileInput").get(0),
            dropRef = drop.get(0);
                
        this.config.upload = { ref : drop };
        
        dropRef.addEventListener('dragover', $.proxy(_handleDragOver, this), false);
        dropRef.addEventListener('drop', $.proxy(_handleFileSelect, this), false);
        inputRef.addEventListener('change', $.proxy(_handleFileInputSelect, this), false);        
    };

    player.setupCallback = function()
    {         
    };

    player.apicontrols_play = function()
    {
        this.config.audio.ref.get(0).play();
        this.onStatusChange("playing");
    };

    player.apicontrols_stop = function()
    {
        var ref = this.config.audio.ref.get(0);
        ref.pause();
        ref.currentTime = 0;
        this.onStatusChange("stopped");    
    };

    player.apicontrols_pause = function()
    {
        this.config.audio.ref.get(0).pause();
        this.onStatusChange("paused");
    };

    player.apicontrols_resume = function()
    {
        this.config.audio.ref.get(0).play();
        this.onStatusChange("playing");
    };
    
    function _addEvents()
    {
        var ref = this.config.audio.ref.get(0);
        ref.addEventListener("timeupdate", $.proxy(_onProgress, this));
        ref.addEventListener("durationchange", $.proxy(_onDurationChange, this));
    };
    
    function _hasAPISupport()
    {
        // Check for the various File API support.
        return (window.File && window.FileReader && window.FileList && window.Blob);
    }
    
    function _hasSongAPISupport()
    {
        return (typeof AudioContext !== "undefined" || typeof webkitAudioContext !== "undefined");
    }
    
    function _handleFileSelect(evt)
    {
        evt.stopPropagation();
        evt.preventDefault();   
        
        this.config.body.ref.removeClass("askingfordrop");
        this.config.body.ref.addClass("parsingmp3");

        // Only use the first FileList object
        _parseGivenFile.call(this, evt.dataTransfer.files[0] );
    }
    
    function _handleFileInputSelect(evt)
    {
        evt.stopPropagation();
        evt.preventDefault();   
        
        this.config.body.ref.removeClass("askingfordrop");
        this.config.body.ref.addClass("parsingmp3");

        // Only use the first FileList object
        _parseGivenFile.call(this, evt.target.files[0] );
    }
    

    function _handleDragOver(evt) {
        evt.stopPropagation();
        evt.preventDefault();        
        evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
    }
    
    function _onProgress()
    {
        var ref = this.config.audio.ref.get(0);
        this.onPositionChange(ref.currentTime);     
    };
    
    function _onDurationChange()
    {            
        var ref = this.config.audio.ref.get(0);            
        
        if( parseInt(ref.duration, 10) < parseInt(this.config.trackDuration, 10) - TRACK_LENGTH_DIFF ||
            parseInt(ref.duration, 10) > parseInt(this.config.trackDuration, 10) + TRACK_LENGTH_DIFF )
        {
            $("#parsingmp3").addClass("lengtherror");
            return;
        }        
        
        this.onTrackChange({                
            name : this.config.trackTitle,
            duration : ref.duration
        });          

        this.onPlayingSourceChanged({                
            artist : this.config.artistName,
            name : this.config.albumName,
            bigIcon : this.config.albumIcon
        });    
        
        this.config.body.ref.removeClass("parsingmp3");
    };
        
    function _parseID3Tag()
    {
        var url = this.data.file.urn || this.data.file.name,
            file = this.data.file;
    
        ID3.loadTags(url, $.proxy(_onID3Loaded, this), { tags: ["title","artist","picture"], dataReader: FileAPIReader(file) });        
    }
    
    function _onFileLoaded(evt)
    {
        this.data.buffer = evt.target.result;
        _parseID3Tag.call(this);
    }
    
    function _onID3Loaded()
    { 
        var url = this.data.file.urn || this.data.file.name,
            tags = ID3.getAllTags(url);
        
        // Now that we have tags, validate that they are similar to the song we want to
        // play
        // TODO: this test is pretty stupid, it'll need to be a bit brighter in the long run
        // use this parce que Louis Maheu a dit que c'est ca que ca prenait : http://en.wikipedia.org/wiki/Levenshtein_distance
        if( tags.artist.toLowerCase() == this.config.artistName.toLowerCase() &&
            tags.title.toLowerCase() == this.config.trackTitle.toLowerCase())
        {            
            
            //  using the <audio> tag            
            var src = (window.URL || window.webkitURL).createObjectURL( this.data.file);
            
            if(!this.config.audio) {
                this.config.audio = {};
                this.config.body.ref.append('<audio></audio>');
                this.config.audio.ref = $("audio");
                _addEvents.call(this);
            }           
                        
            this.config.audio.ref.attr("src", src); 
            this.config.body.ref.focus();
            this.play();             
            
            /*
            // Use the js api
            
            var context = new (window.AudioContext || window.webkitAudioContext)(),
                scope = this;            
            
            // create a buffer source node
            var sourceNode = context.createBufferSource();
            // and connect to destination
            sourceNode.connect(context.destination);
            context.decodeAudioData(this.data.buffer, function(buffer) {
                sourceNode.buffer = buffer;
                scope.play();                
            }, function(){console.log(arguments);});
                        
            this.config.context = context;
            this.config.sourceNode = sourceNode;   
            */
        }
        else {
            $("#parsingmp3").addClass("error");
        }
    }    
    
    function _parseGivenFile(f)
    {
        // Only process audio files.
        if (!f.type.match('audio.mp3'))
        {
            alert("This is not a valid mp3 file.");
            this.config.body.ref.addClass("askingfordrop");
            this.config.body.ref.removeClass("parsingmp3");
            return;
        }       
                                
        var reader = new FileReader();        
        reader.onload = $.proxy(_onFileLoaded, this);
        reader.readAsArrayBuffer(f);
        
        this.data.file = f;
        this.data.reader = reader;
    };    
    
    
    function _readFile(start, limit)
    {
        var buffer = this.data.file,
            binary_string = "",
            bytes;

        if(parseInt(start, 10) >= 0 && parseInt(limit,10) > 0)          
        {
            buffer = buffer.slice(start, limit);
        }
        
        bytes = new Uint8Array(buffer);
        for (var i = 0; i < bytes.byteLength; i++) {
            binary_string += String.fromCharCode(bytes[i]);
        }
        
        return binary_string;
    }
    
})();
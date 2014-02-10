(function() {

    tmt.Rdio = tmt.Player.extend({
               
        run : function()
        {
            this.setupCallback();
            this._super();
        },
               
        loadSongStreamer : function() {    
            if(!swfobject) alert("swfObject is not loaded.");

            var flashvars = {
                "playbackToken": this.config.playbackToken,
                "domain": this.config.domain,
                "listener": "callback_object"
            };

            this.config.container.ref.addClass("loading");        
            swfobject.embedSWF(this.config.swfRoot, this.config.swfId, 1, 1, '9.0.0', null, flashvars, {'allowScriptAccess': 'always'}, {});                
        },
        
        onReady : function(user)
        {        
            this.config.apiswf = $('#' + this.config.swfId).get(0);
            this.config.container.ref.removeClass("loading");

            if(this.config.startOnReady)
            {
                this.play();
            }
        },
        
        setupCallback : function()
        {
            // the global callback object
            var callback_object = window.callback_object = {},
                scope = this; 

            // Called once the API SWF has loaded and is ready to accept method calls
            callback_object.ready = function ready(user) { scope.onUserChange(user); scope.onReady(); };

            // The playback state has changed.
            // The state can be: 0 - paused, 1 - playing, 2 - stopped, 3 - buffering or 4 - paused.
            callback_object.playStateChanged = function playStateChanged(playState) { 
                var playStates = [
                    "paused",
                    "playing",
                    "stopped",
                    "buffering",
                    "paused"
                ];     

               scope.onStatusChange(playStates[playState]);
            };     

            // The currently playing track has changed.
            // Track metadata is provided as playingTrack and the position within the playing source as sourcePosition.
            callback_object.playingTrackChanged = function playingTrackChanged(playingTrack, sourcePosition) {   

                if(scope.isPlayingSong()  && scope.data.frameId > 1 && scope.data.reviewFrames.length > 0 && playingTrack == null)
                {
                    scope.onSongEnd();
                }
                else if(playingTrack.canStream)
                {
                    if (playingTrack != null) scope.onTrackChange(playingTrack);
                    if (sourcePosition !=  null) scope.onPositionChange(sourcePosition);
                }
                else
                {                
                    scope.config.body.ref.addClass("cantstream");
                    scope.stop();
                }

            };

            // The currently playing source changed.
            // The source metadata, including a track listing is inside playingSource.
            callback_object.playingSourceChanged = function playingSourceChanged(playingSource)
            {
                if(playingSource.canStream)
                {
                    scope.onPlayingSourceChanged(playingSource);
                }
            };

            // The volume changed to volume, a number between 0 and 1.
            callback_object.volumeChanged = function volumeChanged(volume) {};

            // Mute was changed. mute will either be true (for muting enabled) or false (for muting disabled).
            callback_object.muteChanged = function muteChanged(mute) {};

            //The position within the track changed to position seconds.
            // This happens both in response to a seek and during playback.
            callback_object.positionChanged = function positionChanged(position) {  
                scope.onPositionChange(position);
            };

            // The queue has changed to newQueue.
            callback_object.queueChanged = function queueChanged(newQueue) {};

            // The shuffle mode has changed.
            // shuffle is a boolean, true for shuffle, false for normal playback order.
            callback_object.shuffleChanged = function shuffleChanged(shuffle) {};

            // The repeat mode change.
            // repeatMode will be one of: 0: no-repeat, 1: track-repeat or 2: whole-source-repeat.
            callback_object.repeatChanged = function repeatChanged(repeatMode) {};

            // An Rdio user can only play from one location at a time.
            // If playback begins somewhere else then playback will stop and this callback will be called.
            callback_object.playingSomewhereElse = function playingSomewhereElse() {};

            // Called with frequency information after apiswf.rdio_startFrequencyAnalyzer(options) is called.
            // arrayAsString is a list of comma separated floats.
            callback_object.updateFrequencyData = function updateFrequencyData(arrayAsString) {  
                scope.onFrequencyChange(arrayAsString.split(',')); 
            };

            return this;
        },
        
        apicontrols_play : function()
        {        
            var apiswf = this.config.apiswf;
            apiswf.rdio_play( this.config.trackKey );
            apiswf.rdio_startFrequencyAnalyzer({
                "frequencies": '31-band',//'raw', // 512 on raw
                "period":  this.config.APP_FRAMERATE
            });
        },

        apicontrols_stop : function()
        {
            var apiswf = this.config.apiswf;
            apiswf.rdio_stop(); 
            apiswf.rdio_stopFrequencyAnalyzer();
        },

        apicontrols_pause : function()
        {
            var apiswf = this.config.apiswf;
            apiswf.rdio_pause();
        },

        apicontrols_resume : function()
        {
            var apiswf = this.config.apiswf;
            apiswf.rdio_play();
        }
        
    });
    
})();
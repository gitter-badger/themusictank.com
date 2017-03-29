(function($, undefined) {

    "use strict";

    var ProfileInitializer = namespace("Tmt.Initializers").ProfileInitializer = function() {
       this.profile = null;
    };

    inherit([ Evemit ], ProfileInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        this.profile = app.profile;
        app.initializers.UpvotesFormsInitializer.on('bound', bindToUpvoteForms.bind(this));
    }

    function bindToUpvoteForms(UpvotesFormsInitializer) {
        for (var i = 0, len = UpvotesFormsInitializer.boxes.length; i < len; i++) {
            var box = UpvotesFormsInitializer.boxes[i];
            box.on("valueChange", onUpvoteValue.bind(this));
        }
    }

    function onUpvoteValue(value, upvoteForm) {
        var type = upvoteForm.isTrack() ? "tracks" : "albums";

        if (value > 0) {
            this.profile.addUpvote(type, upvoteForm.getObjectId(), value);
        } else {
            this.profile.removeUpvote(type, upvoteForm.getObjectId());
        }
    }

}(jQuery));

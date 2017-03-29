(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var UpvoteFormsInitializer = namespace("Tmt.Initializers").UpvoteFormsInitializer = function() {
        this.boxes = [];

        this.events = [
            "bound"
        ];
    };

    inherit([ Evemit ], UpvoteFormsInitializer, {
        'build' : function(app) {
            addEvents.call(this, app);
        }
    });


    function addEvents(app) {
        app.initializers.AjaxFormsInitializer.on('bound', bindToAjaxForms.bind(this));
        app.profile.on('upvoteUpdate', updateBoxesState.bind(this));
    }

    function bindToAjaxForms(AjaxFormsInitializer) {
        var upvoteForms = filter('[data-ctrl="upvote-widget"]', AjaxFormsInitializer.forms);
        for (var i = 0, len = upvoteForms.length; i < len; i++) {
            this.boxes.push(new Tmt.Components.UpvoteForm(upvoteForms[i]));
        }

        this.emit('bound', this);
    }

    function updateBoxesState(newValues) {

    }

}(jQuery));

(function ($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var UpvoteFormsInitializer = namespace("Tmt.Initializers").UpvoteFormsInitializer = function () {
        this.boxes = [];
        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteFormsInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        app.initializers.AjaxFormsInitializer.on('bound', bindToAjaxForms.bind(this));
        app.on('profileFirstPopulated', updateStateFirstTime.bind(this));
    }

    function bindToAjaxForms(AjaxFormsInitializer) {
        var upvoteForms = filter('[data-ctrl="upvote-widget"]', AjaxFormsInitializer.forms);
        for (var i = 0, len = upvoteForms.length; i < len; i++) {
            this.boxes.push(new Tmt.Components.UpvoteForm(upvoteForms[i]));
        }

        this.emit('bound', this);
    }

    function updateStateFirstTime(app, profile) {
        for (var i = 0, len = this.boxes.length; i < len; i++) {
            var box = this.boxes[i],
                matchFound = profile.getVoteByObjectId(box.getType(), box.getObjectId());

            if (matchFound) {
                box.setValue(matchFound);
            }

            // Though we have no value to apply on the control,
            // it is still time to activate it.
            box.unlock();
        }

        this.emit("synched", this);
    }

})(jQuery);

(function ($, undefined) {

    "use strict";

    var boxes = [];

    /**
     * Ajax-enabled forms public bootstraper
     */
    var UpvoteFormsInitializer = namespace("Tmt.Initializers").UpvoteFormsInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteFormsInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        },
        'getForms': function () {
            return boxes;
        }
    });

    function addEvents(app) {
        app.initializers.AjaxFormsInitializer.on('bound', bindToAjaxForms.bind(this));
        app.on('profileFirstPopulated', updateStateFirstTime.bind(this));
    }

    function bindToAjaxForms(ajaxFormsInitializer, forms) {
        filter('[data-ctrl="upvote-widget"]', forms).forEach(function(form){
            boxes.push(new Tmt.Components.UpvoteForm(form));
        });
        this.emit('bound', this);
    }

    function updateStateFirstTime(app, profile) {
        boxes.forEach(function (box) {
            var matchFound = profile.getVoteByObjectId(box.getType(), box.getObjectId());

            if (matchFound) {
                box.setValue(matchFound);
            }

            // Though we have no value to apply on the control,
            // it is still time to activate it.
            box.unlock();
        });

        this.emit("synched", this);
    }

})(jQuery);

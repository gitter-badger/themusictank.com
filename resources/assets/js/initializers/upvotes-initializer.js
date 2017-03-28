(function($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var UpvotesInitializer = namespace("Tmt.Initializers").UpvotesInitializer = function() {
        this.boxes = [];
    };

    UpvotesInitializer.prototype = extend([ Evemit ], {
        'build' : function(app) {
            addEvents(app).bind(this);
        }
    });


    function addEvents(app) {
        app.initializers.AjaxFormsInitializer.on('bound', bindToAjaxForms.bind(this));
    }

    function bindToAjaxForms(AjaxFormsInitializer) {
        var upvoteForms = filter('[data-ctrl="upvote-widget"]', AjaxFormsInitializer.forms),
        for (var i = 0, len = upvoteForms.length; i < len; i++) {
            this.boxes.push(new Tmt.Components.Upvote(upvoteForms[i]));
        }

        this.emit('bound', this);
    }

}(jQuery));

(function ($, undefined) {

    "use strict";

    var ProfileInitializer = namespace("Tmt.Initializers").ProfileInitializer = function () {
        this.profile = null;
        this.notificationTimestamp = 0;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], ProfileInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        this.profile = app.profile;
        
        app.on('ready', bindNotifier.bind(this));
        this.profile.on("dataSet", pingNotifications.call(this));
        app.initializers.UpvoteFormsInitializer.on('bound', bindToUpvoteForms.bind(this));
    }

    function bindToUpvoteForms(UpvoteFormsInitializer) {
        for (var i = 0, len = UpvoteFormsInitializer.boxes.length; i < len; i++) {
            var box = UpvoteFormsInitializer.boxes[i];
            box.on("valueChange", onUpvoteValue.bind(this));
        }
    }

    function bindToProfile() {
        if (this.profile.id > 0) {   
            pingNotifications.call(this);
        }
    }

    function pingNotifications() {    
        this.notificationTimestamp = Date.now();

        $.ajax({
            dataType : "html",
            url : "/ajax/whatsUp/",
            data : { timestamp: this.notificationTimestamp},
            success : function(data) {
                for(var i = 0, len = data.length; i < len; i++) {
                    this.profile.addNotification(data[i]);
                }
                setTimeout(pingNotifications.bind(this), 1000 * 60 * 2);
            }.bind(this)
        });
    }

    function bindNotifier(app) {
        var notifier = new Tmt.Components.Notifer($('[data-ctrl=notifier]'), this.profile);
        notifier.render();
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

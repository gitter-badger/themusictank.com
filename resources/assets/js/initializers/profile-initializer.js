(function ($, undefined) {

    "use strict";

    var ProfileInitializer = namespace("Tmt.Initializers").ProfileInitializer = function () {
        this.notificationTimestamp = 0;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], ProfileInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        app.on('ready', bindProfileHooks.bind(this));
        app.initializers.UpvoteFormsInitializer.on('bound', function(upvoteFormsInitializer){
            bindToUpvoteForms(app, upvoteFormsInitializer);
        }.bind(this));
    }

    function bindProfileHooks(app) {
        app.on("profileFirstPopulated", bindToProfile.bind(this));
    }

    function bindToUpvoteForms(app, UpvoteFormsInitializer) {
        var fn = function(value, upvoteForm) {
            onUpvoteValue(app.profile, value, upvoteForm);
        }.bind(this);

        var i = 0,
            forms = UpvoteFormsInitializer.getForms(),
            len = forms.length;

        for ( ; i < len; i++) {
            forms[i].on("valueChange", fn);
        }
    }

    function bindToProfile(app, profile) {
        if (profile.id > 0) {
            bindNotifier.call(this, app, profile);
            pingNotifications.call(this, profile);
        }
    }

    function bindNotifier(app, profile) {
        var notifier = new Tmt.Components.Notifier($('[data-ctrl=notifier]'), profile);
        notifier.on('notificationRead', clearNotifications.bind(this));
    }

    function pingNotifications(profile) {
        $.ajax({
            dataType : "html",
            url : "/ajax/whatsUp/",
            contentType:"application/json; charset=utf-8",
            data : { timestamp: this.notificationTimestamp},
            success : function(data) {
                data = JSON.parse(data);
                for(var i = 0, len = data.length; i < len; i++) {
                    profile.addNotification(data[i]);
                }
                setTimeout(pingNotifications.bind(this), 1000 * 60 * 2);
            }.bind(this)
        });

        this.notificationTimestamp = parseInt(Date.now() / 1000, 10);
    }

    function clearNotifications(notificationsIds, destinationUrl) {
         $.ajax({
            dataType : "html",
            url : "/ajax/okstfu/",
            contentType:"application/json; charset=utf-8",
            data : { ids: notificationsIds},
            success : function(data) {
                if (destinationUrl) {
                    window.location = destinationUrl;
                }
            }
        });
    }

    function onUpvoteValue(profile, value, upvoteForm) {
        var type = upvoteForm.isTrack() ? "tracks" : "albums";
        if (value > 0) {
            profile.addUpvote(type, upvoteForm.getObjectId(), value);
        } else {
            profile.removeUpvote(type, upvoteForm.getObjectId());
        }
    }

})(jQuery);

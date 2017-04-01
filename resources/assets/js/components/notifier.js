(function() {

    "use strict";

    /**
     * A user notifier control.
     * @param {jQuery} el
     */
    var Notifier = namespace("Tmt.Components").Notifier = function(el, profile) {
        this.element = el;
        this.profile = profile;

        this.initialize();
    };

    inherit([ Tmt.EventEmitter ], Notifier, {
        render : function() {
            this.addEvents();
        },

        addEvents : function() {
            this.profile.on("notification", onNewNotification.bind(this));
            this.emit("bound", this);
        }
    });

    function onNewNotification(notification) {
        console.log(notification);
    }

}());

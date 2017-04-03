(function() {

    "use strict";

    /**
     * A user notifier control.
     * @param {jQuery} el
     */
    var Notifier = namespace("Tmt.Components").Notifier = function(el, profile) {
        this.initialize();

        this.element = el;
        this.notifications = [];

        profile.on("notification", onNewNotification.bind(this));
    };

    inherit([ Tmt.EventEmitter ], Notifier, {
        render : function() {
    var alertCount = 0;

            for(var i = 0; i < this.notifications.length; i++) {
                if (this.notifications[i] && this.notifications[i]['must_notify'] > 0) {
                    alertCount++;
                }
            }

            if (this.notifications.length > 0) {
                this.element.find('li.no-notices').hide();
            } else {
                this.element.find('li.no-notices').show();
            }

            var notice = this.element.find('em');
            notice.html(alertCount);
            if (alertCount > 0) {
                notice.show();
            } else {
                notice.hide();
            }
        }
    });

    function onNewNotification(notification) {
        var label = notification['association_summary'] ? notification['association_summary'] : "Notification";

        // if (notification['association_link']) {
        //     var a = $("<a>");
        //     a.html(label);
        //     a.attr("href", notification['association_link']);
        //     label = a;
        // }

        this.element.find('ul').append('<li class="' + (notification['must_notify'] > 0 ? 'new' : 'old') + '">' + notification['association_summary'] + '</li>');
        this.notifications.push(notification);

        if (this.notifications > 5) {
            this.notifications = 5;
        }

        this.render();
    }

}());

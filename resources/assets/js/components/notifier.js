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

        this.addEvents();
        this.render();
    };

    inherit([ Tmt.EventEmitter ], Notifier, {

        addEvents : function() {
            this.element.find('ul').click(onListClick.bind(this));
            this.element.find('button').click(onClearClick.bind(this));
        },

        render : function() {

            this.hasNotifications() ?
                this.element.find('li.no-notices').hide() :
                this.element.find('li.no-notices').show();


            var alertCount = this.getAlertCount(),
                notice = this.element.find('em');

            notice.html(alertCount);
            alertCount > 0 ?
                notice.show() :
                notice.hide();
        },

        hasNotifications : function() {
            return this.notifications.length > 0;
        },

        getAlertCount : function() {
            var alertCount = 0;
            for(var i = 0; i < this.notifications.length; i++) {
                if (this.notifications[i]['must_notify'] > 0) {
                    alertCount++;
                }
            }
            return alertCount;
        }
    });

    function onListClick(evt) {
        if (evt.target.tagName.toLowerCase() == "a") {
            evt.preventDefault();
            var target = $(evt.target);
            this.emit('notificationRead', [target.data("id")], target.href);

            for(var i = 0; i < this.notifications.length; i++) {
                if (this.notifications[i]['id'] == target.data("id")) {
                    this.notifications[i]['must_notify'] = 0;
                    target.parent().removeClass("new");
                    target.parent().addClass("old");
                    break;
                }
            }

            this.render();
        }
    }

    function onClearClick(evt) {
        var ids = collectNewActivityIds.call(this);
        if (ids.length > 0) {
            for(var i = 0; i < this.notifications.length; i++) {
                this.notifications[i]['must_notify'] = 0;
            }
            this.element.find('li').removeClass("new");
            this.element.find('li').addClass("old");
            this.render();

            this.emit('notificationRead', ids);
        }
    }

    function onNewNotification(notification) {

        var label = notification['association_summary'] ? notification['association_summary'] : "Notification";

        if (notification['associated_object_type'] === "profile") {
            label = '<a data-id="'+ notification['id'] +'" href="/tankers/'+ notification['associated_object']['slug'] +'">' + notification['associated_object']['name'] + ' is now following you.</a>';
        }

        this.element.find('ul').append('<li class="' + (notification['must_notify'] > 0 ? 'new' : 'old') + '">' + label + '</li>');

        this.notifications.push(notification);
        if (this.notifications > 5) {
            this.notifications = 5;
        }

        this.render();
    }

    function collectNewActivityIds() {
        var ids = [];
        for(var i = 0; i < this.notifications.length; i++) {
            if (this.notifications[i]['must_notify'] > 0) {
                ids.push(this.notifications[i]['id']);
            }
        }
        return ids;
    }

}());

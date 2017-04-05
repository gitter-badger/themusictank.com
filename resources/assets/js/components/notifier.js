(function () {

    "use strict";


    var rootNode,
        profile,
        // sound,
        notifications = [];

    /**
     * A user notifier control.
     * @param {jQuery} el
     */
    var Notifier = namespace("Tmt.Components").Notifier = function (el, profileObj) {
        rootNode = el;
        profile = profileObj;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Notifier, {
        'initialize': function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.call(this);
            render.call(this);

            addSound();
        },

        'hasNotifications': function () {
            return notifications.length > 0;
        },

        'getAlertCount': function () {
            var alertCount = 0;
            for (var i = 0; i < notifications.length; i++) {
                if (notifications[i]['must_notify'] > 0) {
                    alertCount++;
                }
            }
            return alertCount;
        }
    });

    function render() {
        this.hasNotifications() ?
            rootNode.find('li.no-notices').hide() :
            rootNode.find('li.no-notices').show();


        var alertCount = this.getAlertCount(),
            notice = rootNode.find('em');

        notice.html(alertCount);
        alertCount > 0 ?
            notice.show() :
            notice.hide();
    }

    function addEvents() {
        rootNode.find('ul').click(onListClick.bind(this));
        rootNode.find('button').click(onClearClick.bind(this));
        profile.on("notification", onNewNotification.bind(this));
    };

    function addSound() {
        // We don't really have a way to telling if the notifications are actually new,
        // or if a user navigated to another page while having unread notifications from the previous page.
        // There are likely other similar cases where it wouldn't make sense to play a sound.
        // Once we figure how to do it well, lets enable it.

        // credit : https://notificationsounds.com/funny/surprise-on-a-spring-496
        // sound = new Howl({ src: ['http://static.themusictank.com/assets/surprise-on-a-spring.mp3'] });
    }

    function onListClick(evt) {
        if (evt.target.tagName.toLowerCase() == "a") {
            evt.preventDefault();
            var target = $(evt.target);

            for (var i = 0; i < notifications.length; i++) {
                if (notifications[i]['id'] == target.data("id")) {
                    notifications[i]['must_notify'] = 0;
                    target.parent().removeClass("new");
                    target.parent().addClass("old");
                    break;
                }
            }

            render.call(this);
            this.emit('notificationRead', [target.data("id")], target.href);
        }
    }

    function onClearClick(evt) {
        var ids = collectNewActivityIds.call(this);
        if (ids.length > 0) {
            for (var i = 0; i < notifications.length; i++) {
                notifications[i]['must_notify'] = 0;
            }
            rootNode.find('li').removeClass("new");
            rootNode.find('li').addClass("old");

            render.call(this);
            this.emit('notificationRead', ids);
        }
    }

    function onNewNotification(notification) {

        var label = notification['association_summary'] ? notification['association_summary'] : "Notification";

        if (notification['associated_object_type'] === "profile") {
            label = '<a data-id="' + notification['id'] + '" href="/tankers/' + notification['associated_object']['slug'] + '">' + notification['associated_object']['name'] + ' is now following you.</a>';
        }

        rootNode.find('ul').append('<li class="' + (notification['must_notify'] > 0 ? 'new' : 'old') + '">' + label + '</li>');

        notifications.push(notification);
        if (notifications > 5) {
            notifications = 5;
        }

        // if (notification['must_notify']) {
        //     this.sound.play();
        // }

        render.call(this);
    }

    function collectNewActivityIds() {
        var ids = [];
        for (var i = 0; i < notifications.length; i++) {
            if (notifications[i]['must_notify'] > 0) {
                ids.push(notifications[i]['id']);
            }
        }
        return ids;
    }

}());

"use strict";

/**
 * Globally exposed namespacing function.
 * @public
 * @param {string} namespace
 * @return {object} A referene to the object created
 */
function namespace(namespace) {
    var object = window, tokens = namespace.split("."), token;

    while (tokens.length > 0) {
        token = tokens.shift();

        if (typeof object[token] === "undefined") {
            object[token] = {};
        }

        object = object[token];
    }

    return object;
}

/**
 * Globally exposed extending function.
 * @param {object} target
 * @param {hash} source
 * @return {object}
 */
function extend(target, source) {
    target = target || {};
    for (var prop in source) {
        if (typeof source[prop] === 'object') {
            target[prop] = extend(target[prop], source[prop]);
        } else {
            target[prop] = source[prop];
        }
    }
    return target;
}

/**
 * Sets up inheritance of the child object to the objects
 * supplied by the parents object.
 * @param {array} parents
 * @param {object} child
 * @param {hash} properties
 * @return {object} An object with inheritance
 */
function inherit(parents, child, properties) {
    var childPrototype = properties;

    for (var i in parents) {
        var parentPrototype = Object.create(parents[i].prototype);
        childPrototype = extend(parentPrototype, childPrototype);
    }

    child.prototype = childPrototype;
    child.prototype.constructor = child;

    return child;
}

/**
 * Globally filters out jQuery elements matching selector
 * from the haystack. This expects javascript objects that
 * have a public "element" property.
 * @param {string} selector
 * @param {array} haystack
 * @return {array} matches
 */
function filter(selector, haystack) {
    var matches = [],
        i = -1;

    while (++i < haystack.length) {
        if (haystack[i].element && haystack[i].element.is(selector)) {
            matches.push(haystack[i]);
        }
    }

    return matches;
}

(function (undefined) {

    "use strict";

    /**
     * @namespace Tmt.EventEmitter
     * @property {array} events A collection of object events and callbacks.
     */
    var EventEmitter = namespace("Tmt").EventEmitter = function() {
        this.events = null;
    };

    inherit([Evemit], namespace("Tmt").EventEmitter, {

        /**
         * Initializes the event emitter object.
         * @method
         * @public
         */
        'initialize': function () {
            // Exposing the creation in a prototype method
            // ensures child classes will have an instantiated value
            // even if they don't go through the constructor.
            this.events = {};
        }
    });

}());

(function (undefined) {

    "use strict";

    /**
     * The global wrapper for the application instance.
     * @namespace Tmt.App
     * @extends {Tmt.EventEmitter}
     * @property {Tmt.Models.Profile} profile - An active session profile
     * @property {Array} initializers - An array of Tmt.Initializers object.
     */
    var App = namespace("Tmt").App = function () {
        this.profile = null;
        this.initializers = [];

        this.initialize();
    };

    inherit([Tmt.EventEmitter], App, {

        /**
         * Boots the application instance
         * @public
         * @method
         */
        boot: function () {
            this.profile = new Tmt.Models.Profile();
            prepareInitializers.call(this);
            this.emit("ready", this);
        },

        /**
         * Assigns session data from PHP to this javascript
         * session instance.
         * @method
         * @public
         */
        setData: function (data) {
            if (data.profile) {
                this.profile.setData(data.profile);
                this.emit('profileFirstPopulated', this, this.profile);
            }

            this.emit('configured', this);
        }
    });

    /**
     * Loads all initializer objects that it can dynamically find
     * in the Tmt.Initializers namespace and then builds them.
     * @method
     * @private
     */
    function prepareInitializers() {
        // Create an intance of each initializer.
        for (var type in Tmt.Initializers) {
            this.initializers[type] = new Tmt.Initializers[type]();
        }

        // Run the initialization. This is done in two steps because
        // initializers may depend on one another.
        for (var type in this.initializers) {
            this.initializers[type].build(this);
        }
    }

}());

(function (undefined) {

    "use strict";

    /**
     * The Profile object is the frontend equivalent of the
     * backend Profile model.
     * @namespace Tmt.Models.Profile
     * @property {array} albumUpvotes
     * @property {array} trackUpvotes
     * @property {array} activities
     */
    var Profile = namespace("Tmt.Models").Profile = function () {
        this.notifications = [];
        this.initialize();
    };

    inherit([Tmt.EventEmitter], Profile, {

        /**
         * Applies backend session data to the object.
         * @param {hash} userData
         * @public
         * @method
         * @fires Profile#upvoteSet
         */
        setData: function (userData) {
            this.username = userData.username;
            this.email = userData.email;
            this.slug = userData.slug;
            this.name = userData.name;
            this.id = userData.id;
            this.emit("dataChange", this);

           // this.albumUpvotes = indexUpvotes("albumUpvotes", userData);
            this.albumUpvotes = userData.albumUpvotes;
            this.emit("upvoteChange", "album", this.albumUpvotes);

            // this.trackUpvotes = indexUpvotes("trackUpvotes", userData);
            this.trackUpvotes = userData.trackUpvotes;
            this.emit("upvoteChange", "track",this.trackUpvotes);
        },

        /**
         * Adds a new vote value to the current profile
         * @param {string} type One of track or album
         * @param {string} key The {type}'s id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        addUpvote: function (type, key, value) {
            if (type == "album") {
                return this.addAlbumUpvote(key, value);
            } else if (type == "track") {
                return this.addTrackUpvote(key, value);
            }
        },

        /**
         * Add a new album vote
         * @param {string} key album id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        addAlbumUpvote: function (key, value) {
            this.albumUpvotes[key] = value;
            this.emit("upvoteUpdate", "album", this.albumUpvotes);
        },

        /**
         * Add a new track vote
         * @param {string} key track id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        addTrackUpvote: function (key, value) {
            this.trackUpvotes[key] = value;
            this.emit("upvoteUpdate", "track", this.trackUpvotes);
        },

        /**
         * Removes an existing vote value to the current profile
         * @param {string} type One of track or album
         * @param {string} key The {type}'s id
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        removeUpvote: function (type, key) {
            if (type == "album") {
                return this.removeAlbumUpvote(key, value);
            } else if (type == "track") {
                return this.removeTrackUpvote(key, value);
            }
        },

        /**
         * Removes an existing album vote
         * @param {string} key album id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        removeAlbumUpvote: function (type, key) {
            delete this.albumUpvotes[key];
            this.emit("upvoteUpdate", "album", this.upvotes);
        },

        /**
         * Removes an existing track vote
         * @param {string} key track id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        removeTrackUpvote: function (type, key) {
            delete this.trackUpvotes[key];
            this.emit("upvoteUpdate", "track", this.upvotes);
        },

        /**
         * Adds a user activity notification (viewed or not)
         * @param {hash} notification
         * @fires Profile#notification
         * @public
         * @method
         */
        addNotification : function (notification) {
            this.notifications.push(notification);

            if (this.notifications.length > 10) {
                this.notifications.length = 10;
            }

            this.emit("notification", notification);
        },

        getVoteByObjectId : function (type, objectId) {
            var match = null;

            if (type == "track") {
                match = this.trackUpvotes[objectId];
            } else if (type == "album") {
                match = this.albumUpvotes[objectId];
            }

            if (match) {
                return match.vote;
            }
        }
    });

    /**
     * Data saved in the database is not easily serachable
     * in javascript. This method bridges the two.
     * @param {string} key one of track or album
     * @param {hash} data values as stored in the BD
     * @return {hash} A javascript-oriented indexed object
     * @private
     * @method
     */
    // function indexUpvotes(key, data) {
    //     var indexed = [];
    //     if (data && data[key]) {
    //         for (var i in data[key]) {
    //             var id = data[key][i].id,
    //                 value = data[key][i].vote;

    //             indexed[id] = value;
    //         }
    //     }
    //     return indexed;
    // }

}());

(function () {

    "use strict";

    var rootNode;

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function (el) {
        rootNode = el;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxForm, {
        initialize: function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.bind(this);
        }
    });


    function addEvents() {
        rootNode.on("submit", onSubmit.bind(this));
        rootNode.on("onBeforeSubmit", onBeforeSubmit.bind(this));
        this.emit("bound", this);
    }

    function onSubmit(event) {
        event.preventDefault();

        this.emit("beforeSubmit", this, event);

        $.ajax({
            url: rootNode.attr("action"),
            data: new FormData(rootNode.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: rootNode.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    };

    function onBeforeSubmit() {
        rootNode.addClass("working");
    }

    function onSubmitSuccess(response) {
        this.emit("submitSuccess", response, this);
    }

}());

(function ($, undefined) {

    "use strict";

    var rootNode,
        ready = false,
        embed = null,
        ytPlayer = null,
        isPlaying = false,
        canSkip = true,
        range = null;

    var Player = namespace("Tmt.Components").Player = function (element) {
        rootNode = element;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], Player, {
        'isReady' : function() {
            return ready;
        },

        'getStreamer' : function() {
            return ytPlayer;
        },

        'render': function () {
            this.hasVideoId() ?
                embedVideo.call(this) :
                queryForKey.call(this);
        },

        'getEmbedId': function () {
            if (this.hasVideoId()) {
                return "tmt_player_" + this.getVideoId();
            }
        },

        'getVideoId': function () {
            return rootNode.data("song-vid");
        },

        'setVideoId': function (id) {
            rootNode.data("song-vid", id);
        },

        'hasVideoId': function () {
            return this.getVideoId() != "";
        },

        'getSongSlug': function () {
            return rootNode.data("song-slug");
        }
    });

    function queryForKey() {
        $.getJSON('/ajax/ytkey/' + this.getSongSlug(), onYtKeyReceived.bind(this));
    }

    function onYtKeyReceived(response) {
        if (response.youtubekey.length === 11) {
            this.setVideoId(response.youtubekey);
            embedVideo.call(this);
        }
    }

    function embedVideo() {
        var id = this.getEmbedId();
        var iframeHtml =
            '<iframe id="' + id + '" scrolling="no" marginwidth="0" ' +
            'marginheight="0" frameborder="0" src="//www.youtube.com/embed/' +
            this.getVideoId() + '?enablejsapi=1&amp;iv_load_policy=3&amp;' +
            'playerapiid=songplayer_component_17&amp;disablekb=1&amp;wmode=transparent&amp;controls=0' +
            '&amp;playsinline=0&amp;showinfo=0&amp;modestbranding=1&amp;rel=0&amp;' +
            'autoplay=0&amp;loop=0&amp;origin=' + window.location.origin + '"></iframe>'

        rootNode.append(iframeHtml);
        embed = $("#" + id);

        ytPlayer = new YT.Player(id);
        ytPlayer.addEventListener('onReady', onPlayerReady.bind(this));
        ytPlayer.addEventListener('onStateChange', onPlayerStateChange.bind(this));

        ready = true;
        this.emit("embeded", this);
    }

    function onPlayerStateChange(newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video queued) */
        var controlButton = rootNode.find('.play');

        if (newState.data === 1) {
            controlButton.removeClass("fa-play");
            controlButton.addClass("fa-pause");

            isPlaying = true;
            this.emit("play");
            onPlayerTick.call(this);
        }
        else if (newState.data === 2) {
            controlButton.removeClass("fa-pause");
            controlButton.addClass("fa-play");

            isPlaying = false;
            this.emit("stop");
        }
    }

    function onProgressClick(e) {
        if (isPlaying) {
            var progressBar = rootNode.find(".progress-wrap .progress"),
                offset = progressBar.offset(),
                relX = e.pageX - offset.left,
                pctLocation = relX / progressBar.width();
            ytPlayer.seekTo(pctLocation * ytPlayer.getDuration(), true);
        }
    };

    function onPlayBtnClick(e) {
        // Ranges wil be back shortly
        this.playingRange = null;

        (ytPlayer.getPlayerState() != 1) ?
            ytPlayer.playVideo() :
            ytPlayer.pauseVideo();
    }


    function onPlayerReady(event) {
        rootNode.find(".duration").html(toReadableTime(ytPlayer.getDuration()));
        rootNode.find(".position").html(toReadableTime(0));
        rootNode.find(".progress-wrap .progress").click(onProgressClick.bind(this));

        var playBtn = rootNode.find('.play');
        playBtn.removeClass("fa-stop");
        playBtn.addClass("fa-play");
        playBtn.click(onPlayBtnClick.bind(this));

        // Ranges wil be back shortly
        // $("*[data-from]").click(function () {
        //     var el = $(this);
        //     tmt.playingRange = [parseInt(el.attr("data-from"), 10), parseInt(el.attr("data-to"), 10)];
        //     (player.getPlayerState() != 1) ? player.playVideo() : player.pauseVideo();
        //     player.seekTo(tmt.playingRange[0], true);
        // });
    };

    function onPlayerTick() {
        var currentTime = ytPlayer.getCurrentTime(),
            durationTime = ytPlayer.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        rootNode.find('.position').html(toReadableTime(currentTime));

        rootNode.find('.cursor').css("left", currentPositionPct + "%");
        rootNode.find('.progress .loaded-bar').css("width", (ytPlayer.getVideoLoadedFraction() * 100) + "%");
        rootNode.find('.progress .playing-bar').css("width", currentPositionPct + "%");
        rootNode.find('.progress .playing-bar').attr("aria-valuenow", currentTime);

        if (isPlaying) {
            // if (tmt.playingRange) {

            //     if (currentTime >= tmt.playingRange[1]) {
            //         tmt.playingRange = null;
            //         player.pauseVideo();
            //     }
            //     else if (currentTime <= tmt.playingRange[0]) {
            //         player.seekTo(tmt.playingRange[0], true);
            //     }
            // }
            setTimeout(onPlayerTick.bind(this), 250);
        }
    }

    function toReadableTime(seconds) {
        var time = new Date(1000 * seconds),
            mins = ("0" + time.getMinutes()).slice(-2),
            secs = ("0" + time.getSeconds()).slice(-2);

        return mins + ":" + secs;
    }

})(jQuery);

(function ($, undefined) {

    "use strict";

    var ajaxForm,
        rootNode,
        enabled = false;

    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxFormObj) {
        ajaxForm = ajaxFormObj;
        rootNode = ajaxForm.element;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.bind(this);
            resetButtons();
        },

        "getType": function () {
            return rootNode.data("upvote-type");
        },

        "getObjectId": function () {
            return rootNode.data("upvote-object-id");
        },

        "setObjectId": function (id) {
            return rootNode.data("upvote-object-id", id);
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            rootNode.removeClass("liked disliked");
            rootNode.find("input[name=vote]").val(value);

            if (value == 1) {
                rootNode.addClass("liked");
                enableButton(rootNode.find('button.up'));
            } else if (value == 2) {
                rootNode.addClass("disliked");
                rootNode.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons();
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return rootNode.find("input[name=vote]").val();
        },

        "lock": function () {
            enabled = false;
            rootNode.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            enabled = true;
            rootNode.find("button").removeAttr("disabled");
        }
    });

    function addEvents() {
        rootNode.find("button").click(onButtonClick.bind(this));
        ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
    };

    function resetButtons() {
        rootNode.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        rootNode.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
    }

    function enableButton(button) {
        if (button.hasClass("up")) {
            button.html('<i class="fa fa-thumbs-up" aria-hidden="true">');
        }
        if (button.hasClass("down")) {
            button.html('<i class="fa fa-thumbs-down" aria-hidden="true">');
        }
    }

    function onButtonClick(evt) {
        if (!enabled) {
            return;
        }

        var $el = $(evt.target),
            button = $el.parents('button'),
            clickedValue = button.val();

        if (clickedValue != this.getValue()) {
            this.setValue(clickedValue);
        } else {
            // twice the same value means the user wants to cancel
            this.setValue(-1);
        }

        this.lock();
        rootNode.submit();
    }

    function onSubmitSuccess(response, ajaxForm) {
        if (response && response.vote) {
            this.setObjectId(response.id);
            this.setValue(response.vote);
        }

        this.unlock();
    }

})(jQuery);


(function ($, undefined) {

    "use strict";

    var SearchForm = namespace("Tmt.Components").SearchForm = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], SearchForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            // search box
            var artistsSearch = new Bloodhound({
                name: 'artists',
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('artist'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/ajax/artistSearch/?q=%QUERY',
                    wildcard: '%QUERY'
                }
            }),
                albumsSearch = new Bloodhound({
                    name: 'albums',
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('album'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/ajax/albumSearch/?q=%QUERY',
                        wildcard: '%QUERY'
                    }
                }),
                tracksSearch = new Bloodhound({
                    name: 'tracks',
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('track'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/ajax/trackSearch/?q=%QUERY',
                        wildcard: '%QUERY'
                    }
                }),
                searchBox = $('.typeahead');


            // Listens for when Typeahead a selected a value.
            function typeahead_onSelected(e, data, section) {
                e.preventDefault();
                document.location = $('.tt-cursor a:nth-child(1)').attr('href');
            }

            artistsSearch.initialize();
            albumsSearch.initialize();
            tracksSearch.initialize();

            searchBox.on("typeahead:selected", typeahead_onSelected);

            searchBox.typeahead(
                { minLength: 3, highlight: true, cache: true },
                [
                    {
                        name: 'artists',
                        display: 'artist',
                        source: artistsSearch,
                        cache: true,
                        templates: {
                            header: '<h3>Artists</h3>',
                            empty: '<h3>Artists</h3><p class="empty-message">Could not find matching artists.</p>',
                            suggestion: function (data) { return ['<p><a href="/artists/' + data.slug + '/">' + data.name + '</a></p>'].join(""); }
                        }
                    },
                    {
                        name: 'albums',
                        display: 'album',
                        source: albumsSearch,
                        cache: true,
                        templates: {
                            header: '<h3>Albums</h3>',
                            empty: '<h3>Albums</h3><p class="empty-message">Could not find matching albums.</p>',
                            suggestion: function (data) { return ['<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
                        }
                    },
                    {
                        name: 'tracks',
                        display: 'track',
                        source: tracksSearch,
                        cache: true,
                        templates: {
                            header: '<h3>Tracks</h3>',
                            empty: '<h3>Tracks</h3><p class="empty-message">Could not find matching tracks.</p>',
                            suggestion: function (data) { return ['<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
                        }
                    }
                ]
            );

        }
    });


})(jQuery);

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

(function ($, undefined) {

    "use strict";

    var track = null,
        knob = null,
        enabled = false,
        draggable = null;

    var Knob = namespace("Tmt.Components.Reviewer").Knob = function (element) {
        track = element;
        knob = element.find('b');
        enabled = false;

        addEvents();
    };

    inherit([], Knob, {
        enable: function () {
            track.removeClass("disabled");
            track.addClass("enabled");

            draggable.enable();

            enabled = true;
        },

        disable: function () {
            track.addClass("disabled");
            track.removeClass("enabled");

            draggable.disable();

            enabled = false;
        },

        setValue: function (value) {
            knob.css("top", (value * 100) + "%");
        }
    });

    function addEvents() {
        draggable = Draggable.create(knob.get(0), {
            type: "y",
            bounds: track.get(0)
        })[0];
    }


})(jQuery);

(function ($, undefined) {

    "use strict";


    // var GROOVE_DECAY_VALUE  = 0.0005,
    //     MIDDLE_GROOVE_VALUE = 0.500,
    //     FRAMERATE           = 24,
    //     FRAMES_PER_SAVE     = null,

    //     SAVE_FRAMERATE       = 3 / FRAMERATE * 1000,
    //     MAX_MULTIPLIER      = 3,

    //     // These define the length of all 'animations'
    //     DURATION_COMPARE    = 3.5 * 60, // the basis for all effects is based on 1 sec when song is 3m30s
    //     LENGTH_TO_SHAKE     = 0.1,
    //     //LENGTH_POWERING     = 4.2,
    //     LENGTH_SHAKING      = 1.8,
    //     //LENGTH_TO_MULTIPLIER = 3,
    //     //LENGTH_MULTIPLYING  = 3.5,

    //     //FRAMES_PER_POWERING = null,
    //     FRAMES_TO_SHAKE     = null,
    //     FRAME_PER_SHAKE     = null
    //     //FRAMES_PER_MULTIPLIER  = null,
        //FRAMES_PER_MULTIPLY = null,

       // HIGH_GROOVE_MULTIPLIER_THRESHOLD = .75,
        //LOW_GROOVE_MULTIPLIER_THRESHOLD = .25,
        //HIGH_GROOVE_THRESHOLD = .98,
        //LOW_GROOVE_THRESHOLD = .02;
;


// var //currentGroove = MIDDLE_GROOVE_VALUE,
//     decay = 0,
//     multiplier = 0,
//     reviewFrames = [],
//     appHasFocus = true,
//     savetick = 0,
//     lastframeSent = 0,
//     songIsOver = false,

//     domCache = [];

    var NEUTRAL_GROOVE_POINT = 0.500,
        GROOVE_DECAY        = 0.0005,
        FRAMERATE           = 24,
        SAVE_FRAMERATE      = 3 / FRAMERATE * 1000,
        FRAMES_TO_SHAKE     = FRAMERATE * LENGTH_TO_SHAKE,
        FRAME_PER_SHAKE     = FRAMERATE * LENGTH_SHAKING,
        FRAMES_PER_SAVE     = FRAMERATE * 5,
        LENGTH_TO_SHAKE     = 0.1,
        LENGTH_SHAKING      = 1.8;

    var currentGroove,
        player,
        knob,
        rootNode;


    var Reviewer = namespace("Tmt.Components.Reviewer").Reviewer = function (element, playerObj) {
        rootNode = element;
        player = playerObj;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Reviewer, {

        'initialize' : function()
        {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents();
            setGrooveTo(NEUTRAL_GROOVE_POINT);
            start();
        }
    });

    function addEvents()
    {
        registerKnob();

        player.on("play", onPlay);
        player.on("stop", onStop);
    }

    function setGrooveTo(value) {
        currentGroove = value;
        knob.setValue(value);
    }

    function start() {
        player.getStreamer().playVideo();
    }

    function onPlay() {
        knob.enable();
    }

    function onStop() {
        knob.disable();
    }

    function registerKnob() {
        knob = new Tmt.Components.Reviewer.Knob(rootNode.find(".knob-track"));
    }

})(jQuery);

/*
        addEvents : function()
        {
            this._super();

            $(window).resize( $.proxy(this.onWindowResize, this) );
            this.config.joystick.ref.draggable({
                containment : "parent",
                axis        : "y",
                disabled    : false,
                start       : $.proxy(this.onGrooveStart, this),
                stop        : $.proxy(this.onGrooveStop, this),
                drag        : $.proxy(function(event, ui) { if(!this.isPlayingSong()) return false;}, this)
            });

            $('body>div').bind("dragstart", function(event, ui){
                  event.stopPropagation();
            });
        },

        getContextSizes : function()
        {
            this.config.canvas = {ref : this.config.container.ref.find("canvas")};
            if(this.config.canvas)
            {
                var ref = this.config.canvas.ref,
                    node = ref.get(0);

                this.config.sizes = {
                    top     : 0,
                    left    : 0,
                    height  : ref.height(),
                    width   : ref.width()
                };

                node.height = ref.height();
                node.width = ref.width();
                this.config.context = node.getContext("2d");
            }
        },

        addJoystick : function()
        {
            var joy = this.element.find(".joystick"),
                b = joy.find("b");

            this.joystickData = {
                containerRef    : joy,
                containerHeight : joy.innerHeight(),
                ref             : b,
                height          : b.outerHeight(),
                actualHeight    : 0,
                containerPosition : null
            };

            this.config.joystick.actualHeight = this.config.joystick.containerHeight - this.config.joystick.height;

            joy.css({
                "top" : (this.config.container.height/2) - (this.config.joystick.containerHeight / 2),
                "left": (this.config.container.width/2) - (joy.outerWidth() / 2)
            });
            this.config.joystick.containerPosition = joy.position();

            this.centerGrooveKnob();
        },

        centerGrooveKnob : function()
        {
            if(this.config.joystick)
            {
                this.config.joystick.ref.css("top", ((this.config.joystick.containerHeight / 2) - (this.config.joystick.height / 2)) + "px");
            }
        },

        animate : function()
        {
            if(this.data.animating)
            {
                if(!this.appHasFocus())
                {
                    this.focusLostError();
                }
                else
                {
                    if(this.frameShouldRender())
                    {
                        this.tick();
                        this.render();
                    }
                }


                var scope = this;
                requestAnimationFrame(function() { scope.animate(); });
            }
        },

        frameShouldSave : function()
        {
            return  !this.data.paused && (this.data.savetick + SAVE_FRAMERATE <= this.getNow());
        },

        render : function()
        {
            try {
                this.calculateGrooveCurve();
                this.displayFrequency();
                this.displayGroove();
                this.updateMultiplierUi();
                this.updateUiStatuses();
                this.addClasses();
                this.runGuiFx();

                this._super();
            } catch(e) {
                console.trace(e.message);
            }
        },

        displayGroove : function()
        {
            var groove = parseInt(this.data.groove*100, 10),
                context = this.config.context;

            if(groove > 99) groove = "∞";

            context.beginPath();
            context.arc(50, this.config.sizes.height - 50, 35, 0, 2 * Math.PI, false);
            context.fillStyle = 'rgba(250,250,250,.8)';
            context.fill();
            context.closePath();

            context.font = '20pt Tahoma';
            context.fillStyle = '#000';
            context.fillText(groove + "", 35, this.config.sizes.height - 35);

            if(!this.data.grooving)
            {
                var position = Math.abs(this.data.groove-1) * this.config.joystick.actualHeight;
                this.config.joystick.ref.css("top", position + "px");
            }
        },

        displayFrequency : function()
        {
            var frequency = this.data.frequency,
                i = 0,
                len = frequency.length,
                context = this.config.context,
                containerHeight = this.config.sizes.height,
                width = Math.ceil(this.config.sizes.width / len);

            context.clearRect (0, 0, this.config.sizes.width, this.config.sizes.height);

            for(var freq, color, height; i < len; i++)
            {
                freq = frequency[i];
                height = containerHeight * freq;
                color = parseInt(255 * freq, 10);

                if(height > containerHeight) height = containerHeight;
                else if(height < 1) height = 1;

                if(color > 250) color = 250;
                else if(color < 40) color = 40;

                context.beginPath();
                context.rect(i * width, containerHeight - height, width, height);
                context.fillStyle = 'rgb('+ color +','+ color +','+ color +')';
                context.fill();
            }
        },


        updateUiStatuses : function()
        {
            var container = this.config.container,
                context = this.config.context;

            if(this.data.multiplier !== 0)
            {
                context.beginPath();
                context.rect(20, 20, 50, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("X " + this.data.multiplier, 20, 50);
            }

            if(this.data.suckpowering)
            {
                context.beginPath();
                context.rect(20, 50, 150, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("Suckpowering!", 20, 75);
            }

            if(this.data.starpowering)
            {
                context.beginPath();
                context.rect(20, 50, 150, 50);
                context.fillStyle = 'rgba(0,0,0,.8)';
                context.fill();

                context.font = '20pt Tahoma';
                context.fillStyle = '#fff';
                context.fillText("Starpowering!", 20, 75);
            }
        },

        addClasses : function()
        {
            var container = this.config.container;

            if(this.data.shaking !== container.shaking)
            {
                this.data.shaking ?
                    container.ref.addClass("shaking") :
                    container.ref.removeClass("shaking");
                container.shaking = this.data.shaking;
            }
            /*
            if(this.data.starpowering !== container.starpowering)
            {
                this.data.starpowering ?
                    container.ref.addClass("starpowering") :
                    container.ref.removeClass("starpowering");
                container.starpowering = this.data.starpowering;
            }

            if(this.data.suckpowering !== container.suckpowering)
            {
                this.data.suckpowering ?
                    container.ref.addClass("suckpowering") :
                    container.ref.removeClass("suckpowering");
                container.suckpowering = this.data.suckpowering;
            }

            if(this.data.multiplier !== container.multiplier)
            {
                container.ref.removeClass("multiplier_" + container.multiplier);
                container.ref.addClass("multiplier_" + this.data.multiplier);
                container.multiplier = this.data.multiplier;
            }     * /
            /*
            if(this.data.status !== container.status)
            {
                container.ref.removeClass("status_" + container.status);
                container.ref.addClass("status_" + this.data.status);
                container.status = this.data.status;
            }* /

            if(this.data.synchronising !== container.synchronising)
            {
                this.data.synchronising ?
                    container.ref.addClass("synchronising") :
                    container.ref.removeClass("synchronising");
                container.multiplier = this.data.multiplier;
            }

            /* handled in canvas now.
            if(this.data.timers.positiveMultiplierStart !== container.buildingPositiveMultiplier)
            {
                this.data.timers.positiveMultiplierStart ?
                    container.ref.addClass("multiplier-build-up") :
                    container.ref.removeClass("multiplier-build-up");
                container.buildingPositiveMultiplier = this.data.timers.positiveMultiplierStart;
            }

            if(this.data.timers.negativeMultiplierStart !== container.buildingNegativeMultiplier)
            {
                this.data.timers.negativeMultiplierStart ?
                    container.ref.addClass("multiplier-build-down") :
                    container.ref.removeClass("multiplier-build-down");
                container.buildingNegativeMultiplier = this.data.timers.negativeMultiplierStart;
            }       * /
        },

        runGuiFx : function()
        {
            this.shakeJoystick();
        },

        shakeJoystick : function()
        {
            if(!this.data.shaking && !this.config.joystick.moved) return;

            var originalTopValue = this.config.joystick.containerPosition.top,
                originalLeftValue = this.config.joystick.containerPosition.left,
                topValue = null,
                leftValue = null,
                random = 0;

            if(this.data.shaking)
            {
                random = Math.random();
                topValue = (random <= 0.5) ? originalTopValue + 2 : originalTopValue - 2;
                random = Math.random();
                leftValue = (random <= 0.5) ? originalLeftValue + 2 : originalLeftValue - 2;
                this.config.joystick.moved = true;
            }
            else if(this.config.joystick.moved)
            {
                this.config.joystick.moved = false;
                topValue = originalTopValue;
                leftValue = originalLeftValue;
            }

            this.config.joystick.containerRef.css({
                top : topValue + "px",
                left : leftValue + "px"
            });
        },

        updateMultiplierUi : function()
        {
            if(!this.data.timers.positiveMultiplierStart && !this.data.timers.negativeMultiplierStart)
                return;

            var pct = 0, deg, styles,
                context = this.config.context,
                level = Math.abs(this.data.multiplier);

            if(this.data.timers.positiveMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.positiveMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }

            else if(this.data.timers.negativeMultiplierStart)
            {
                 pct = (this.data.frameId - this.data.timers.negativeMultiplierStart) / FRAMES_PER_MULTIPLIER;
            }

            if(level < 3)
            {
                // Draw the previous underlying stroke
                if(level > 0)
                {
                    context.beginPath();
                    context.arc(50, this.config.sizes.height - 50, 40, 0, 2 * Math.PI, false);
                    context.lineWidth = 12;
                    context.strokeStyle = 'rgba(0,0,0,' + (0.3 * level) + ')';
                    context.stroke();
                    context.closePath();
                }

                context.beginPath();
                context.arc(50, this.config.sizes.height - 50, 40, 0, (2*pct) * Math.PI, false);
                context.lineWidth = 12;
                context.strokeStyle = 'rgba(0,0,0,' + (0.3 * (level+1)) + ')';
                context.stroke();
                context.closePath();
            }
            else
            {
                context.beginPath();
                context.arc(50, this.config.sizes.height - 50, 40, 0, 2 * Math.PI, false);
                context.lineWidth = 12;
                context.strokeStyle = '#FF0000';
                context.stroke();
                context.closePath();
            }
        },

        toggleMultiplier : function(isPositive)
        {
            if(isPositive)
            {
                if(this.data.multiplier < MAX_MULTIPLIER)
                {
                    if(this.data.multiplier < -1) this.data.multiplier = 0;
                    this.data.multiplier++;
                    this.data.timers.activeMultiplierStart = this.data.frameId;
                    this.debug("data", "Multiplier positive applied", this.data.multiplier);
                }
            }
            else
            {
                if(this.data.multiplier > -MAX_MULTIPLIER)
                {
                    if(this.data.multiplier > 1) this.data.multiplier = 0;
                    this.data.multiplier--;
                    this.data.timers.activeMultiplierStart = this.data.frameId;
                    this.debug("data", "Multiplier negative applied", this.data.multiplier);
                }
            }
        },

        sendFramesPackage : function(idxStart, idxEnd)
        {
            this.data.synchronising = true;

            $.ajax(this.config.tmtUrl, {
                type : "POST",
                data: { frames : this.data.reviewFrames.slice(idxStart, idxEnd) },
                success : $.proxy(this.onSyncSuccess, this),
                error : $.proxy(this.onSyncFail, this)
            });
        },

        appHasFocus : function()
        {
            return this.data.appHasFocus === true;
        },

        focusLostError : function()
        {
            if(this.isPlayingSong())
            {
                var scope = this,
                    container = this.config.container;

                if(!this.config.container.isFocusLost)
                {
                    var btn = container.ref.find(".focus-lost button"),
                        onclick = function() {
                            container.ref.removeClass("focuslost");
                            container.isFocusLost = false;
                            btn.unbind("click");
                            scope.resume();
                        };

                    this.pause();
                    this.config.container.isFocusLost = true;
                    btn.click(onclick);
                    container.ref.addClass("focuslost");
                    btn.focus();
                }
            }
        },

        // LOOPS - The Math
        // Tick contains the calulations that have no direct visual impact
        tick : function()
        {
            try {
                this.logFrame();
                this.checkGrooveIntensity();
                this.checkStarpowers();
                this.checkMultiplier();
                this.checkMultiplierTimers();
                this.decayGrooveCurve();
                this.saveCurrentFrame();
                this.saveEquilizerFrame();
            } catch(e) {
                console.error(e);
                console.trace(e.message);
            }
        },

        logFrame : function()
        {
            this.data.frameId++;
            this.data.tick = this.getNow();
        },

        saveCurrentFrame : function()
        {
            if(this.frameShouldSave())
            {
                var lastFrame = null,
                    idx = null
                    currentFrame = {
                        gv  : this.data.groove,
                        st  : this.data.starpowering === true ? 1 : 0,
                        su  : this.data.suckpowering === true ? 1 : 0,
                        m   : this.data.multiplier,
                        p   : this.data.position,
                        o   : 0
                   };

                if(this.data.reviewFrames.length > 0)
                {
                    lastFrame = this.data.reviewFrames[this.data.reviewFrames.length - 1];
                }

                if(lastFrame && (lastFrame["p"] != currentFrame["p"] || lastFrame["gv"] != currentFrame["gv"]) )
                {
                    this.data.reviewFrames.push(currentFrame);
                    this.data.savetick = this.getNow();
                }
                else if(!lastFrame)
                {
                    this.data.reviewFrames.push(currentFrame);
                    this.data.savetick = this.getNow();
                }
            }

            if(this.data.frameId % FRAMES_PER_SAVE === 0)
            {
                this.sendFramesPackage(this.data.lastframeSent, this.data.reviewFrames.length);
                this.data.lastframeSent = this.data.reviewFrames.length - 1;
            }
        },

        calculateGrooveCurve : function()
        {
            if(this.config.joystick)
            {
                var value       = parseFloat(this.config.joystick.ref.css("top")),
                    height      = this.config.joystick.actualHeight,
                    pctOfHeight = value / height,
                    computedValue = (1 - pctOfHeight) + this.data.decay,
                    centerRange = GROOVE_DECAY_VALUE * 2;

                this.data.groove = computedValue;

                if      (this.data.groove < 0)  this.data.groove = 0;
                else if (this.data.groove > 1)  this.data.groove = 1;
                else if (this.data.groove > (MIDDLE_GROOVE_VALUE - centerRange) && this.data.groove < (MIDDLE_GROOVE_VALUE + centerRange))
                                                this.data.groove = 0.5;
            }
        },

        checkStarpowers : function()
        {
            if(this.data.starpowering)
            {
                if(this.data.timers.starpowerStart + FRAMES_PER_POWERING <= this.data.frameId)
                {
                    this.data.timers.starpowerStart = null;
                    this.data.starpowering = false;
                }
            }

            if(this.data.suckpowering)
            {
                if(this.data.timers.suckpowerStart + FRAMES_PER_POWERING <= this.data.frameId)
                {
                    this.data.timers.suckpowerStart = null;
                    this.data.suckpowering = false;
                }
            }
        },

        checkGrooveIntensity : function()
        {
            if(this.data.grooving)
            {
                // liking it a lot
                if(this.data.groove >= HIGH_GROOVE_THRESHOLD && !this.data.starpowering)
                {
                    this.data.timers.lowGrooveStart = null;

                    if(!this.data.timers.highGrooveStart)
                    {
                        this.data.timers.highGrooveStart = this.data.frameId;
                    }
                    else if(this.data.timers.highGrooveStart + FRAME_PER_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = false;
                        this.data.timers.highGrooveStart = null;
                        this.data.starpowering = true;
                        this.data.timers.starpowerStart = this.data.frameId;
                    }
                    else if(this.data.timers.highGrooveStart + FRAMES_TO_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = true;
                    }
                    return;
                }
                // hating it a lot
                else if(this.data.groove <= LOW_GROOVE_THRESHOLD && !this.data.suckpowering)
                {
                    this.data.timers.highGrooveStart = null;
                    if(!this.data.timers.lowGrooveStart)
                    {
                        this.data.timers.lowGrooveStart = this.data.frameId;
                    }
                    else if(this.data.timers.lowGrooveStart + FRAME_PER_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = false;
                        this.data.suckpowering = true;
                        this.data.timers.lowGrooveStart = null;
                        this.data.timers.suckpowerStart = this.data.frameId;
                    }
                    else if(this.data.timers.lowGrooveStart + FRAMES_TO_SHAKE <= this.data.frameId)
                    {
                        this.data.shaking = true;
                    }
                    return;
                }
            }

            // Reset if the groove is in between
            this.data.shaking = false;
            this.data.timers.lowGrooveStart = null;
            this.data.timers.highGrooveStart = null;
        },

        /**
         * Checks the global mutiplier timer to set or remove the multiplier
         * flag. Also triggers
         * /
        checkMultiplier : function()
        {
            // if the timer is triggered, user is multiplying from liking a long time
            if(this.data.timers.activeMultiplierStart)
            {
                // Cancel out the multiplier timer, animation is complete
                if(this.data.timers.activeMultiplierStart + FRAMES_PER_MULTIPLY <= this.data.frameId)
                {
                    this.data.multiplier = 0;
                    this.data.timers.activeMultiplierStart = null;
                }
            }
            // if no timer is set, but we are powering start it right away
            else if(this.data.starpowering)
            {
                this.toggleMultiplier(true);
            }
            else if(this.data.suckpowering)
            {
                this.toggleMultiplier(false);
            }
        },

        checkMultiplierTimers : function()
        {
            // If user is not actively pressing on the knob, no
            // multiplier can become active;
            if(this.data.grooving)
            {
                if(this.data.groove >= HIGH_GROOVE_MULTIPLIER_THRESHOLD)
                {
                    // When there is enjoyment, cancel out any negative
                    // multiplers
                    this.data.timers.negativeMultiplierStart = null;
                    if(this.data.multiplier < 0) this.data.multiplier = 0;

                    if(!this.data.timers.positiveMultiplierStart)
                    {
                        this.data.timers.positiveMultiplierStart = this.data.frameId;
                    }
                    else if(this.data.timers.positiveMultiplierStart + FRAMES_PER_MULTIPLIER <= this.data.frameId)
                    {
                        this.toggleMultiplier(true);
                        this.data.timers.positiveMultiplierStart = null;
                    }
                    return;
                }
                else if(this.data.groove <= LOW_GROOVE_MULTIPLIER_THRESHOLD)
                {
                    // When there isnt enjoyment, cancel out any positive
                    // multiplers
                    this.data.timers.positiveMultiplierStart = null;
                    if(this.data.multiplier > 0) this.data.multiplier = 0;

                    if(!this.data.timers.negativeMultiplierStart)
                    {
                        this.data.timers.negativeMultiplierStart = this.data.frameId;
                    }
                    else if(this.data.timers.negativeMultiplierStart + FRAMES_PER_MULTIPLIER <= this.data.frameId)
                    {
                        this.toggleMultiplier(false);
                        this.data.timers.negativeMultiplierStart = null;
                    }
                    return;
                }
            }

            this.data.timers.positiveMultiplierStart = null;
            this.data.timers.negativeMultiplierStart = null;
        },

        decayGrooveCurve : function()
        {
            if(this.config.joystick)
            {
                this.data.decay = 0;
                if(!this.data.grooving && !this.data.starpowering && !this.data.suckpowering)
                {
                    if      (this.data.groove > MIDDLE_GROOVE_VALUE) this.data.decay = -GROOVE_DECAY_VALUE;
                    else if (this.data.groove < MIDDLE_GROOVE_VALUE) this.data.decay = GROOVE_DECAY_VALUE;
                }
            }
        },

        onSongEnd : function()
        {
            this._super();

            this.data.songIsOver = true;
            this.config.container.ref.addClass("loading");

            var last = this.data.reviewFrames[this.data.reviewFrames.length - 1];
            if(this.data.lastframeSent < this.data.reviewFrames.length)
            {
                last["o"] = 1;
            }
            // Make sure we send in at least a frame.
            else
            {
                last["o"] = 1;
                this.data.reviewFrames.push(last);
            }

            this.sendFramesPackage(this.data.lastframeSent, this.data.reviewFrames.length);
        },

        onGrooveStart : function()
        {
            this.data.grooving = true;
        },

        onGrooveStop : function()
        {
            this.data.grooving = false;
        },

        onSyncSuccess : function()
        {
            this.data.synchronising = false;

            if(this.data.songIsOver)
            {
                this.config.container.ref.removeClass("loading");
                this.config.container.ref.addClass("completed");
            }
        },

        onSyncFail : function()
        {
            this.data.synchronising = false;
        },

        isPlayingSong : function()
        {
            return this.data.status === "playing";
        },

        onWindowResize : function()
        {
            //this.setDisplayHeight();
        },

        onWindowVisibility : function(isVisible)
        {
            this.data.appHasFocus = isVisible;
            this.debug("data", "appHasFocus", this.data.appHasFocus);
        }*/


(function ($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function () {
        this.forms = [];
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxFormsInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function bindPageForms() {
        var forms = [];

        $("form[data-ctrl-mode=ajax]").each(function () {
            forms.push(new Tmt.Components.AjaxForm($(this)));
        });

        this.forms = forms;
        this.emit('bound', this);
    }

    function addEvents(app) {
        app.on('ready', bindPageForms.bind(this));
    }

})(jQuery);

(function ($, undefined) {

    "use strict";

    var PlayerInitializer = namespace("Tmt.Initializers").PlayerInitializer = function () {
        this.initialize();
        this.players = [];
    };

    inherit([Tmt.EventEmitter], PlayerInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        $(onDomReady.bind(this));
    }

    function onDomReady() {
        var components = $("*[data-song-vid]");
        if (components.length > 0) {
            for (var i = 0; i < components.length; i++) {
                this.players.push(new Tmt.Components.Player($(components.get(i))));
            }
            includeYoutubeScript.call(this);
        }
        this.emit('bound', this);
    }

    function onYouTubeReady() {
        for (var i = 0; i < this.players.length; i++) {
            this.players[i].render();
        }
    }

    function includeYoutubeScript() {
        var tag = document.createElement('script');
        tag.src = "//www.youtube.com/player_api";

        window.onYouTubeIframeAPIReady = onYouTubeReady.bind(this);

        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

})(jQuery);

(function ($, undefined) {

    "use strict";

    var reviewerWindow;

    var ReviewerInitializer = namespace("Tmt.Initializers").ReviewerInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], ReviewerInitializer, {
        'build': function (app) {
            reviewerWindow = $('[data-attr="tmt-reviewer"]');
            if (reviewerWindow.length > 0) {
                app.initializers.PlayerInitializer.on('bound', bindToPlayer.bind(this));
            }
        }
    });

    function bindToPlayer(playerInitializer) {
        if (playerInitializer.players.length === 1) {
            playerInitializer.players[0].on('embeded', function(player) {
                player.getStreamer().addEventListener('onReady', function() {
                    new Tmt.Components.Reviewer.Reviewer(reviewerWindow, player);
                });
            });
        }
    }

})(jQuery);

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
        app.initializers.UpvoteFormsInitializer.on('bound', function(UpvoteFormsInitializer){
            bindToUpvoteForms(app, UpvoteFormsInitializer);
        }.bind(this));
    }

    function bindProfileHooks(app) {
        app.on("profileFirstPopulated", bindToProfile.bind(this));
    }

    function bindToUpvoteForms(app, UpvoteFormsInitializer) {
        var fn = function(value, upvoteForm) {
            onUpvoteValue(app.profile, value, upvoteForm);
        }.bind(this);

        for (var i = 0, len = UpvoteFormsInitializer.boxes.length; i < len; i++) {
            var box = UpvoteFormsInitializer.boxes[i];
            box.on("valueChange", fn);
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

(function ($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var SearchInitializer = namespace("Tmt.Initializers").SearchInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], SearchInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function bindForm() {
        new Tmt.Components.SearchForm();
    }

    function addEvents(app) {
        app.on('ready', bindForm.bind(this));
    }

})(jQuery);

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

//# sourceMappingURL=app.js.map

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
 * have a public "getRootNode" method.
 * @param {string} selector
 * @param {array} haystack
 * @return {array} matches
 */
function filter(selector, haystack) {
    var matches = [];

    haystack.forEach(function (hay) {
        var node = hay.getRootNode();
        if (node && node.is(selector)) {
            matches.push(hay);
        }
    });

    return matches;
}


function debounce(func, threshold, execAsap) {
    var timeout;

    return function debounced() {
        var obj = this, args = arguments;
        function delayed() {
            if (!execAsap)
                func.apply(obj, args);
            timeout = null;
        };

        if (timeout)
            clearTimeout(timeout);
        else if (execAsap)
            func.apply(obj, args);

        timeout = setTimeout(delayed, threshold || 100);
    };
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

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function (el) {
        this.rootNode = el;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxForm, {
        initialize: function () {
            Tmt.EventEmitter.prototype.initialize.call(this);
            addEvents.call(this);
        },

        getRootNode: function () {
            return this.rootNode;
        }
    });


    function addEvents() {
        this.rootNode.on("submit", onSubmit.bind(this));
        this.emit("bound", this);
    }

    function onSubmit(event) {
        event.preventDefault();

        this.rootNode.addClass("working");
        this.emit("beforeSubmit", this, event);

        $.ajax({
            url: this.rootNode.attr("action"),
            data: new FormData(this.rootNode.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: this.rootNode.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    }

    function onSubmitSuccess(response) {
        this.rootNode.removeClass("working");
        this.emit("submitSuccess", response, this);
    }

}());

(function ($, undefined) {

    "use strict";

    var Player = namespace("Tmt.Components").Player = function (element) {
        this.rootNode = element;
        this.ready = false;
        this.embed = null;
        this.ytPlayer = null;
        this.playing = false;
        this.canSkip = true;
        this.range = null;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Player, {
        'isReady' : function() {
            return this.ready;
        },

        'isPlaying' : function() {
            return this.playing;
        },

        'getStreamer' : function() {
            return this.ytPlayer;
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
            return this.rootNode.data("song-vid");
        },

        'setVideoId': function (id) {
            this.rootNode.data("song-vid", id);
        },

        'hasVideoId': function () {
            return this.getVideoId() != "";
        },

        'getSongSlug': function () {
            return this.rootNode.data("song-slug");
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

        this.rootNode.append(iframeHtml);
        this.embed = $("#" + id);

        this.ytPlayer = new YT.Player(id);
        this.ytPlayer.addEventListener('onReady', onPlayerReady.bind(this));
        this.ytPlayer.addEventListener('onStateChange', onPlayerStateChange.bind(this));

        this.ready = true;
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
        var controlButton = this.rootNode.find('.play');

        if (newState.data === 1) {
            controlButton.removeClass("fa-play");
            controlButton.addClass("fa-pause");

            this.playing = true;
            this.emit("play");
            onPlayerTick.call(this);
        }
        else if (newState.data === 2) {
            controlButton.removeClass("fa-pause");
            controlButton.addClass("fa-play");

            this.playing = false;
            this.emit("stop");
        }
    }

    function onProgressClick(e) {
        if (this.playing) {
            var progressBar = this.rootNode.find(".progress-wrap .progress"),
                offset = progressBar.offset(),
                relX = e.pageX - offset.left,
                pctLocation = relX / progressBar.width();
            this.ytPlayer.seekTo(pctLocation * this.ytPlayer.getDuration(), true);
        }
    };

    function onPlayBtnClick(e) {
        // Ranges wil be back shortly
        this.playingRange = null;

        (this.ytPlayer.getPlayerState() != 1) ?
            this.ytPlayer.playVideo() :
            this.ytPlayer.pauseVideo();
    }


    function onPlayerReady(event) {
        this.rootNode.find(".duration").html(toReadableTime(this.ytPlayer.getDuration()));
        this.rootNode.find(".position").html(toReadableTime(0));
        this.rootNode.find(".progress-wrap .progress").click(onProgressClick.bind(this));

        var playBtn = this.rootNode.find('.play');
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
        var currentTime = this.ytPlayer.getCurrentTime(),
            durationTime = this.ytPlayer.getDuration(),
            currentPositionPct = (currentTime / durationTime) * 100;

        this.rootNode.find('.position').html(toReadableTime(currentTime));

        this.rootNode.find('.cursor').css("left", currentPositionPct + "%");
        this.rootNode.find('.progress .loaded-bar').css("width", (this.ytPlayer.getVideoLoadedFraction() * 100) + "%");
        this.rootNode.find('.progress .playing-bar').css("width", currentPositionPct + "%");
        this.rootNode.find('.progress .playing-bar').attr("aria-valuenow", currentTime);

        if (this.playing) {
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


    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxFormObj) {
        this.ajaxForm = ajaxFormObj;
        this.rootNode = ajaxFormObj.getRootNode();
        this.enabled = false;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            addEvents.call(this);
            resetButtons.call(this);

            this.rootNode.addClass('initialized');
        },

        "getType": function () {
            return this.rootNode.data("upvote-type");
        },

        "getObjectId": function () {
            return this.rootNode.data("upvote-object-id");
        },

        "setObjectId": function (id) {
            return this.rootNode.data("upvote-object-id", id);
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            this.rootNode.removeClass("liked disliked");
            this.rootNode.find("input[name=vote]").val(value);

            if (value == 1) {
                this.rootNode.addClass("liked");
                enableButton(this.rootNode.find('button.up'));
            } else if (value == 2) {
                this.rootNode.addClass("disliked");
                this.rootNode.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons.call(this);
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return this.rootNode.find("input[name=vote]").val();
        },

        "lock": function () {
            this.enabled = false;
            this.rootNode.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            this.enabled = true;
            this.rootNode.find("button").removeAttr("disabled");
        }
    });

    function addEvents() {
        this.rootNode.find("button").click(onButtonClick.bind(this));
        this.ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
    };

    function resetButtons() {
        this.rootNode.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        this.rootNode.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
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
        if (!this.enabled) {
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
        this.rootNode.submit();
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
                            suggestion: function (data) { return ['<p><a href="/tracks/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
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

    var Knob = namespace("Tmt.Components.Reviewer").Knob = function (element) {
        this.track = element;
        this.knob = element.find('b');

        this.enabled = false;
        this.working = false;
        this.position = null;
        this.trackHeight = null;
        this.draggable = null;
        this.nudged = false;

        this.value = 0;

        addEvents.call(this);
        saveCurrentPosition.call(this);
    };

    inherit([], Knob, {
        enable: function () {
            this.track.removeClass("disabled");
            this.track.addClass("enabled");

            this.draggable.enable();
            this.enabled = true;
        },

        disable: function () {
            this.track.addClass("disabled");
            this.track.removeClass("enabled");

            this.draggable.disable();
            this.enabled = false;
        },

        setValue: function (value) {
            this.value = value;

            if (!this.working) {
                var topPosition = this.trackHeight * (1 - value);
                TweenMax.set(this.knob.get(0), { css: { y:  topPosition } });
                this.draggable.update();
            }
        },

        getValue: function () {
            if (this.working) {
                var value = 1 - (this.draggable.y / this.trackHeight);
            } else {
                var value = this.value;
            }
            
            // Ensure we don't break boundries
            if (value > 1)  {
                return 1;
            } else if (value < 0) {
                return 0;
            }

            return value;
        },

        isWorking : function() {
            return this.working;
        },

        isEnabled : function() {
            return this.enabled;
        },

        stopCurrentDrag : function() {
            this.draggable.disable();
            this.draggable.enable();
        },

        nudge : function() {
            this.nudged = true;
            this.track.css({
                'margin-top' : (Math.random() <= 0.5 ?  2 : -2) + "px",
                'margin-left' : (Math.random() <= 0.5 ?  2 : -2) + "px"
            });
        },

        center : function() {
            if (this.nudged) {
                this.track.css({
                    'margin-top' : null,
                    'margin-left' : null
                });
            }
        }
    });

    function saveCurrentPosition() {
        this.position = this.track.position();
        this.trackHeight = this.track.innerHeight() - this.knob.outerHeight();
    }

    function addEvents() {
        this.draggable = Draggable.create(this.knob.get(0), {
            type: "y",
            bounds: this.track.get(0),
            onDragStart: onDragStart.bind(this),
            onDragEnd: onDragEnd.bind(this),
        })[0];
    }

    function onDragStart() {
        this.working = true;
    }

    function onDragEnd() {
        this.working = false;
    }

})(jQuery);

(function ($, undefined) {

    "use strict";

    var NEUTRAL_GROOVE_POINT = 0.500,
        GROOVE_DECAY = 0.0005,
        FRAMERATE = 26,
        SAVE_FRAMERATE = 3 / FRAMERATE * 1000,
        HIGH_GROOVE_THRESHOLD = 0.98,
        LOW_GROOVE_THRESHOLD = 0.02,
        LENGTH_TO_SHAKE = 0.65 * FRAMERATE,
        LENGTH_PER_SHAKE = 1.75 * FRAMERATE;


    var Reviewer = namespace("Tmt.Components.Reviewer").Reviewer = function (element, playerObj) {
        this.rootNode = element;
        this.player = playerObj;
        this.shaking = false;

        this.timers = {
            highGrooveStart: null,
            lowGrooveStart: null,
        };
        this.currentGroove = 0;
        this.currentFrameId = 0;
        this.drawnFrameId = null;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Reviewer, {

        'initialize': function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            registerKnob.call(this);
            registerCanvas.call(this);
            addEvents.call(this);
            setGrooveTo.call(this, NEUTRAL_GROOVE_POINT);
            start.call(this);
        }
    });

    function addEvents() {
        this.player.on("play", onPlay.bind(this));
        this.player.on("stop", onStop.bind(this));
    }

    function setGrooveTo(value) {
        this.currentGroove = value;
        this.knob.setValue(value);
    }

    function start() {
        this.player.getStreamer().playVideo();
    }

    function onPlay() {
        this.knob.enable();
        tick.call(this);
        animate.call(this);
    }

    function onStop() {
        this.knob.disable();
    }

    function registerKnob() {
        this.knob = new Tmt.Components.Reviewer.Knob(this.rootNode.find(".knob-track"));
    }

    function registerCanvas() {
        this.canvas = new Tmt.Components.Reviewer.Canvas(this.rootNode.find("canvas"));

        this.canvas.addEmitter("positiveSpark", this.canvas.node.width / 2, this.canvas.node.height * .15);
        this.canvas.addEmitter("negativeSpark", this.canvas.node.width / 2, this.canvas.node.height * .85);
    }

    function tick() {
        if (this.player.isPlaying()) {
            setFrameContext.call(this);
            calculateTimelineContext.call(this);
            calculateGroove.call(this);
            // saveFrameBAtch

            setTimeout(tick.bind(this), 1000 / FRAMERATE);
        }
    }

    function animate() {
        if (this.drawnFrameId != this.currentFrameId) {
            this.drawnFrameId = this.currentFrameId;
            paintFrame.call(this);
            requestAnimationFrame(animate.bind(this));
        }
    }

    function isPositive() {
        return this.currentGroove > NEUTRAL_GROOVE_POINT;
    }

    function isNegative() {
        return this.currentGroove < NEUTRAL_GROOVE_POINT;
    }

    function setFrameContext() {
        this.currentFrameId++;

        if (this.currentFrameId > 100000) {
            this.currentFrameId = 1;
        }
    }

    function paintFrame() {
        if (this.shaking) {
            this.knob.nudge();
        } else {
            this.knob.center();
        }

        this.canvas.draw();
    }

    function calculateGroove() {
        if (this.knob.isWorking()) {
            this.currentGroove = this.knob.getValue();
        } else if (isPositive.call(this)) {
            this.currentGroove -= GROOVE_DECAY;
        } else if (isNegative.call(this)) {
            this.currentGroove += GROOVE_DECAY;
        }

        if (
            this.currentGroove > (NEUTRAL_GROOVE_POINT - (GROOVE_DECAY * 2)) &&
            this.currentGroove < (NEUTRAL_GROOVE_POINT + (GROOVE_DECAY * 2))
        ) {
            this.currentGroove = NEUTRAL_GROOVE_POINT;
        }

        this.knob.setValue(this.currentGroove);
    }

    function calculateTimelineContext() {
        if (this.knob.isWorking()) {
            if (this.currentGroove > HIGH_GROOVE_THRESHOLD) {
                this.timers.lowGrooveStart = null;
                calculatePositiveContext.call(this);
                return;
            } else if (this.currentGroove < LOW_GROOVE_THRESHOLD) {
                this.timers.highGrooveStart = null;
                calculateNegativeContext.call(this);
                return;
            }
        }

        this.timers.lowGrooveStart = null;
        this.timers.highGrooveStart = null;
        this.shaking = false;
    }

    // liking it a lot
    function calculatePositiveContext() {
        if (!this.timers.highGrooveStart) {
            this.timers.highGrooveStart = this.currentFrameId;
            this.shaking = true;
            this.canvas.emit("positiveSpark", 10);

        } else if (this.timers.highGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.highGrooveStart = null;
            this.currentGroove = HIGH_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.shaking = false;
            this.canvas.emit("positiveSpark", 100);
        }
    }

    // hating it a lot
    function calculateNegativeContext() {
        if (!this.timers.lowGrooveStart) {
            this.timers.lowGrooveStart = this.currentFrameId;
            this.shaking = true;
            this.canvas.emit("negativeSpark", 10);

        } else if (this.timers.lowGrooveStart + LENGTH_PER_SHAKE <= this.currentFrameId) {
            this.timers.lowGrooveStart = null;
            this.currentGroove = LOW_GROOVE_THRESHOLD;
            this.knob.stopCurrentDrag();
            this.shaking = false;
            this.canvas.emit("negativeSpark", 100);
        }
    }

    function sendFramesPackage (idxStart, idxEnd) {
        this.synchronising = true;

        $.ajax("/ajax/savegroove", {
            type: "POST",
            data: { frames: this.data.reviewFrames.slice(idxStart, idxEnd) },
            success: $.proxy(this.onSyncSuccess, this),
            error: $.proxy(this.onSyncFail, this)
        });
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

    var Canvas = namespace("Tmt.Components.Reviewer").Canvas = function (element) {
        this.rootNode = element;
        this.node = element.get(0);
        this.context = this.node.getContext('2d');
        this.emitters = {};

        addEvents.call(this);
    };

    inherit([], Canvas, {

        addEmitter : function(id, x, y) {
            var emitter = new Tmt.Components.Reviewer.Emitter.ParticleEmitter();
            emitter.setCanvas(this.node); 
            emitter.moveTo(x, y);

            this.emitters[id] = emitter;
        },

        emit : function(id, qty) {  
            this.emitters[id].start(qty);
        },

        draw : function() {
            this.context.clearRect(0, 0, this.node.width, this.node.height);
            for(var i in  this.emitters) {
                if (this.emitters[i].isRunning()) {
                    this.emitters[i].run();
                    this.emitters[i].render();
                }
            }
        }

    });

    function addEvents () {
        $(window).on('resize', debounce(applyCurrentSize.bind(this)));
        applyCurrentSize.call(this);
    }

    function applyCurrentSize () {
        this.node.height = this.rootNode.parent().height();
        this.node.width = this.rootNode.parent().width();
    }


})(jQuery);

(function ($, undefined) {

    "use strict";

    var Vector = namespace("Tmt.Components.Reviewer.Emitter").Vector = function (x, y) {
        this.y = y;
        this.x = x;
    };

    inherit([], Vector, {

        add : function (vector) {
            this.x = this.x + vector.x;
            this.y = this.y + vector.y;
        }

    });

})(jQuery);

(function ($, undefined) {

    "use strict";

    var Vector = Tmt.Components.Reviewer.Emitter.Vector;

    var Particle = namespace("Tmt.Components.Reviewer.Emitter").Particle = function (canvas, x, y) {
        this.size = Math.random() * 10 + 15;

        this.position = new Vector(x, y);
        var velocityX = (Math.random() * 5) * (Math.random() >= 0.5 ? 1 : -1);
        var velocityY = Math.random() * 5;

        this.velocity = new Vector(velocityX, velocityY);
        this.acceleration = new Vector(0, 0.1);
        this.lifespan = Math.random() * 350;

        this.image = new Image();
        this.image.src = '/assets/img/spark.png';
        this.context = canvas.getContext('2d');

        this.image.onload = function () {
            this.context.drawImage(this.image, this.position.x, this.position.y);
        }.bind(this);
    };

    inherit([], Particle, {

        update: function () {
            this.velocity.add(this.acceleration);
            this.position.add(this.velocity);
            this.lifespan -= 1;
        },

        isDead: function () {
            return this.lifespan < 0;
        },

        paint: function () {
            this.context.save();
            if (this.lifespan < 10) {
                this.context.globalAlpha = this.lifespan / 100;
            }
            
            this.context.drawImage(this.image, this.position.x, this.position.y);
            this.context.restore();
        }

    });


})(jQuery);

(function ($, undefined) {

    "use strict";

    var Particle = Tmt.Components.Reviewer.Emitter.Particle,
        Vector = Tmt.Components.Reviewer.Emitter.Vector;

    var ParticleEmitter = namespace("Tmt.Components.Reviewer.Emitter").ParticleEmitter = function () {
        this.particles = [];
        this.position = new Vector(0, 0);
    };

    inherit([], ParticleEmitter, {

        isRunning: function () {
            return this.particles.length > 0;
        },

        setCanvas: function (canvas) {
            this.canvas = canvas;
        },

        moveTo: function (x, y) {
            this.position = new Vector(x, y);
        },

        start: function (quantity) {
            for (var i = 0; i < quantity; i++) {
                this.particles.push(new Particle(this.canvas, this.position.x, this.position.y));
            }
        },

        run: function () {
            for (var i = this.particles.length - 1; i >= 0; i--) {
                this.particles[i].update();
                if (this.particles[i].isDead()) {
                    this.particles.pop();
                }
            }
        },

        render: function () {
            for (var i = this.particles.length - 1; i >= 0; i--) {
                this.particles[i].paint();
            }
        }

    });


})(jQuery);

(function ($, undefined) {

    "use strict";

    var forms = [];

    /**
     * Ajax-enabled forms public bootstraper
     */
    var AjaxFormsInitializer = namespace("Tmt.Initializers").AjaxFormsInitializer = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], AjaxFormsInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        },

        'getForms': function() {
            return forms;
        }
    });

    function bindPageForms() {
        forms = [];
        $("form[data-ctrl-mode=ajax]").each(function () {
            forms.push(new Tmt.Components.AjaxForm($(this)));
        });

        this.emit('bound', this, forms);
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

//# sourceMappingURL=app.js.map

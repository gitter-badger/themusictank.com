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
            this.emit("ready");
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
            }
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
     */
    var Profile = namespace("Tmt.Models").Profile = function () {
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
            this.albumUpvotes = indexUpvotes("albumUpvotes", userData);
            this.trackUpvotes = indexUpvotes("trackUpvotes", userData);

            if (this.albumUpvotes && this.albumUpvotes.length > 0) {
                this.emit("upvoteSet", "album", this.albumUpvotes);
            }

            if (this.trackUpvotes && this.trackUpvotes.length > 0) {
                this.emit("upvoteSet", "track", this.trackUpvotes);
            }
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
    function indexUpvotes(key, data) {
        var indexed = [];
        if (data && data[key]) {
            for (var i in data[key]) {
                var id = data[key][i].id,
                    value = data[key][i].vote;

                indexed[id] = value;
            }
        }
        return indexed;
    }

}());

(function() {

    "use strict";

    /**
     * A form object that can be captured using ajax.
     * @param {jQuery} el
     */
    var AjaxForm = namespace("Tmt.Components").AjaxForm = function(el) {
        this.element = el;
        this.initialize();
    };

    inherit([ Tmt.EventEmitter ], AjaxForm, {
        render : function() {
            this.addEvents();
        },

        addEvents : function() {
            this.element.on("submit", onSubmit.bind(this));
            this.element.on("onBeforeSubmit", onBeforeSubmit.bind(this));
            this.emit("bound", this);
        }
    });


    function onSubmit(event) {
        event.preventDefault();

        this.emit("beforeSubmit", this, event);

        var formElement = this.element;

        $.ajax({
            url: formElement.attr("action"),
            data: new FormData(formElement.get(0)),
            cache: false,
            processData: false,
            contentType: false,
            type: formElement.attr('method'),
            success: onSubmitSuccess.bind(this)
        });

        this.emit("submit", this);
    };

    function onBeforeSubmit()
    {
        this.element.addClass("working");
    }

    function onSubmitSuccess(response) {
        /*
        var newVersion = $(html);

        this.element.replaceWith(newVersion);
        this.element = newVersion;
        this.addEvents();*/

        this.emit("submitSuccess", response, this);
        // this.emit("afterRender", this);
    }

}());

(function ($, undefined) {

    "use strict";

    var Player = namespace("Tmt.Components").Player = function (element) {
        this.element = element;
        this.embed = null;
        this.ytPlayer = null;
        this.isPlaying = false;
        this.range = null;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], Player, {

        'render': function () {
            this.hasVideoId() ?
                embedVideo.call(this) :
                queryForKey.call(this);
        },

        'getEmbedId': function () {
            if (this.hasVideoId) {
                return "tmt_player_" + this.getVideoId();
            }
        },

        'getVideoId': function () {
            return this.element.data("song-vid");
        },

        'setVideoId': function (id) {
            this.element.data("song-vid", id);
        },

        'hasVideoId': function () {
            return this.getVideoId() != "";
        },

        'getSongSlug': function () {
            return this.element.data("song-slug");
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

        this.element.append(iframeHtml);
        this.embed = $("#" + id);

        this.ytPlayer = new YT.Player(id);

        this.ytPlayer.addEventListener('onReady', onPlayerReady.bind(this));
        this.ytPlayer.addEventListener('onStateChange', onPlayerStateChange.bind(this));
    }

    function onPlayerStateChange(newState) {
        /*
        -1 (unstarted)
        0 (ended)
        1 (playing)
        2 (paused)
        3 (buffering)
        5 (video queued) */
        var controlButton = this.element.find('.play');

        if (newState.data === 1) {
            controlButton.removeClass("fa-play");
            controlButton.addClass("fa-pause");

            this.isPlaying = true;
            onPlayerTick.call(this);
        }
        else if (newState.data === 2) {
            controlButton.removeClass("fa-pause");
            controlButton.addClass("fa-play");

            this.isPlaying = false;
        }
    }

    function onProgressClick(e) {
        if (this.isPlaying) {
            var progressBar = this.element.find(".progress-wrap .progress"),
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
        this.element.find(".duration").html(toReadableTime(this.ytPlayer.getDuration()));
        this.element.find(".position").html(toReadableTime(0));
        this.element.find(".progress-wrap .progress").click(onProgressClick.bind(this));

        var playBtn = this.element.find('.play');
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

        this.element.find('.position').html(toReadableTime(currentTime));

        this.element.find('.cursor').css("left", currentPositionPct + "%");
        this.element.find('.progress .loaded-bar').css("width", (this.ytPlayer.getVideoLoadedFraction() * 100) + "%");
        this.element.find('.progress .playing-bar').css("width", currentPositionPct + "%");
        this.element.find('.progress .playing-bar').attr("aria-valuenow", currentTime);

        if (this.isPlaying) {
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

    var UpvoteForm = namespace("Tmt.Components").UpvoteForm = function (ajaxForm) {
        this.ajaxForm = ajaxForm;
        this.element = ajaxForm.element;

        this.initialize();
    };

    inherit([Tmt.EventEmitter], UpvoteForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            this.addEvents();
            resetButtons.call(this);
            this.element.addClass("initialized");
        },

        "addEvents": function () {
            this.element.find("button").click(onButtonClick.bind(this));
            this.ajaxForm.on('submitSuccess', onSubmitSuccess.bind(this));
        },

        "getType": function () {
            return this.element.data("upvote-type");
        },

        "getObjectId": function () {
            return this.element.data("upvote-object-id");
        },

        "isTrack": function () {
            return this.getType() == "track";
        },

        "isAlbum": function () {
            return this.getType() == "album";
        },

        "setValue": function (value) {
            this.element.removeClass("liked disliked");
            this.element.find("input[name=vote]").val(value);

            if (value == 1) {
                this.element.addClass("liked");
                enableButton(this.element.find('button.up'));
            } else if (value == 2) {
                this.element.addClass("disliked");
                this.element.find('button.down').html('<i class="fa fa-thumbs-down" aria-hidden="true">');
            } else {
                resetButtons.call(this);
            }

            this.emit('valueChange', value, this);
        },

        "getValue": function () {
            return this.element.find("input[name=vote]").val();
        },

        "lock": function () {
            this.element.find("button").attr("disabled", "disabled");
        },

        "unlock": function () {
            this.element.find("button").removeAttr("disabled");
        }
    });

    function resetButtons() {
        this.element.find('button.up').html('<i class="fa fa-thumbs-o-up" aria-hidden="true">');
        this.element.find('button.down').html('<i class="fa fa-thumbs-o-down" aria-hidden="true">');
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
        var clickedValue = evt.target.value;

        if (clickedValue != this.getValue()) {
            this.setValue(clickedValue);
        } else {
            // twice the same value means the user wants to cancel
            this.setValue(-1);
        }

        this.lock();
        this.element.submit();
    }

    function onSubmitSuccess(response, ajaxForm) {
        if (response && response.vote) {
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
                            suggestion: function (data) { return ['<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.slug + '/">' + data.name + '</a></p>'].join(""); }
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
                            suggestion: function (data) { return ['<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.slug + '/">' + data.name + '</a></p>'].join(""); }
                        }
                    }
                ]
            );

        }
    });


})(jQuery);

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
            var form = new Tmt.Components.AjaxForm($(this));
            form.render();
            forms.push(form);
        });

        this.forms = forms;
        this.emit('bound', this);
    }

    function addEvents(app) {
        app.on('ready', bindPageForms.bind(this));
    }

}(jQuery));

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

}(jQuery));

(function ($, undefined) {

    "use strict";

    var ProfileInitializer = namespace("Tmt.Initializers").ProfileInitializer = function () {
        this.profile = null;
        this.initialize();
    };

    inherit([Tmt.EventEmitter], ProfileInitializer, {
        'build': function (app) {
            addEvents.call(this, app);
        }
    });

    function addEvents(app) {
        this.profile = app.profile;
        app.initializers.UpvoteFormsInitializer.on('bound', bindToUpvoteForms.bind(this));
    }

    function bindToUpvoteForms(UpvoteFormsInitializer) {
        for (var i = 0, len = UpvoteFormsInitializer.boxes.length; i < len; i++) {
            var box = UpvoteFormsInitializer.boxes[i];
            box.on("valueChange", onUpvoteValue.bind(this));
        }
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

}(jQuery));

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
        app.profile.on('upvoteSet', updateStateFirstTime.bind(this));
    }

    function bindToAjaxForms(AjaxFormsInitializer) {
        var upvoteForms = filter('[data-ctrl="upvote-widget"]', AjaxFormsInitializer.forms);

        for (var i = 0, len = upvoteForms.length; i < len; i++) {
            this.boxes.push(new Tmt.Components.UpvoteForm(upvoteForms[i]));
        }

        this.emit('bound', this);
    }

    function updateStateFirstTime(type, newValues) {

        for (var i = 0, len = this.boxes.length; i < len; i++) {
            var box = this.boxes[i];

            if (box.getType() == type) {
                var matching = newValues[box.getObjectId()];
                if (matching) {
                    box.setValue(matching);
                }

                box.unlock();
            }
        }

        this.emit("completed");
    }

}(jQuery));

//# sourceMappingURL=app.js.map

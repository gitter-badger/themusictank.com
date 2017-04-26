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

    inherit(['Tmt.EventEmitter'], Profile, {

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

            // this.albumUpvotes = indexUpvotes("albumUpvotes", userData);
            this.albumUpvotes = userData.albumUpvotes || {};

            // this.trackUpvotes = indexUpvotes("trackUpvotes", userData);
            this.trackUpvotes = userData.trackUpvotes || {};

            this.emit("dataChange", this);
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
            this.albumUpvotes[key+''] = {'id': key, 'vote': value};
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
            this.trackUpvotes[key+''] = {'id': key, 'vote': value};
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
                return this.removeAlbumUpvote(key);
            } else if (type == "track") {
                return this.removeTrackUpvote(key);
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
        removeAlbumUpvote: function (key) {
            delete this.albumUpvotes[key];
            this.emit("upvoteUpdate", "album", this.albumUpvotes);
        },

        /**
         * Removes an existing track vote
         * @param {string} key track id
         * @param {string} value
         * @fires Profile#upvoteUpdate
         * @public
         * @method
         */
        removeTrackUpvote: function (key) {
            delete this.trackUpvotes[key];
            this.emit("upvoteUpdate", "track", this.trackUpvotes);
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

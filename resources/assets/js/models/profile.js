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
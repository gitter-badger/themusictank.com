(function (undefined) {

    "use strict";

    var Profile = namespace("Tmt.Models").Profile = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], Profile, {

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

        addUpvote: function (type, key, value) {
            if (type == "album") {
                return this.addAlbumUpvote(key, value);
            } else if (type == "track") {
                return this.addTrackUpvote(key, value);
            }
        },

        addAlbumUpvote: function (key, value) {
            this.albumUpvotes[key] = value;
            this.emit("upvoteUpdate", "album", this.albumUpvotes);
        },

        addTrackUpvote: function (key, value) {
            this.trackUpvotes[key] = value;
            this.emit("upvoteUpdate", "track", this.trackUpvotes);
        },

        removeUpvote: function (type, key) {
            if (type == "album") {
                return this.removeAlbumUpvote(key, value);
            } else if (type == "track") {
                return this.removeTrackUpvote(key, value);
            }
        },

        removeAlbumUpvote: function (type, key) {
            delete this.albumUpvotes[key];
            this.emit("upvoteUpdate", "album", this.upvotes);
        },

        removeTrackUpvote: function (type, key) {
            delete this.trackUpvotes[key];
            this.emit("upvoteUpdate", "track", this.upvotes);
        }
    });

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

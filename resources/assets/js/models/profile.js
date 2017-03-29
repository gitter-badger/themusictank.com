(function (undefined) {

    "use strict";

    var Profile = namespace("Tmt.Models").Profile = function(userData) {
        this.events = [
            "upvoteUpdate"
        ];

        this.upvotes = {
            tracks : {},
            albums : {}
        };
        this.setData(userData);
    };

    inherit([ Evemit ], Profile, {

        'setData' : function(userData) {
            for(var i in userData) {
                this[i] = userData[i];
            }

            this.emit("upvoteSet", this.upvotes);
        },

        'addUpvote' : function (type, key, value) {
            this.upvotes[type][key] = value;
            this.emit("upvoteUpdate", this.upvotes);
        },

        'removeUpvote' : function(type, key) {
            delete this.upvotes[type][key];
            this.emit("upvoteUpdate", this.upvotes);
        }

    });

}());

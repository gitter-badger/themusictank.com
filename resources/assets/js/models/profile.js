(function (undefined) {

    "use strict";

    var Profile = namespace("Tmt.Models").Profile = function(userData) {
        this.events = [
            "upvoteUpdate"
        ];

        for(var i in userData) {
            this[i] = userData[i];
        }
    };

    inherit([ Evemit ], Profile, {

    });

}());

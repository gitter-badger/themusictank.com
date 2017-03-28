(function($, tmt, undefined) {

    "use strict";

    var Profile = tmt.Profile = function(data) {
        for(var i in data) {
            this[i] = data[i];
        }
    }

}( window.tmt = window.tmt || {}, jQuery ));

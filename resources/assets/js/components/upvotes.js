(function($, TMT, undefined) {

    "use strict";

    TMT.Components.Upvotes = function(instances) {
        var i = -1,
            upvotes = [];

        while (++i < instances.length) {
            var upvote = new Upvote(instances[i]);
            upvotes.push(upvote);
        };

        return upvotes;
    };

    var Upvote = function(form) {
        this.form = form;
    };

    Upvote.prototype = {

    };

})(jQuery, tmt);

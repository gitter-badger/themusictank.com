(function () {

    "use strict";

    // Setup app namespacing
    window.tmt = {
        'Components': {}
    };

    var App = window.tmt.App = function App(data) {
        this.userData = data;
    }

    App.prototype = {
        'init': function () {
            var forms = tmt.Components.AjaxForms();

            var upvotes = tmt.Components.Upvotes(
                filter('[data-ctrl="upvote-widget"]', forms),
                this.userData.upvotes || []
            );
        }
    };

    function filter(selector, haystack) {
        var matches = [],
            i = -1;

        while (++i < haystack.length) {
            if (haystack[i].element && haystack[i].element.is('selector')) {
                matches.push(haystack[i]);
            }
        }

        return matches;
    }

})();

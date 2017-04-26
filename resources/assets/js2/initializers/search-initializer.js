(function ($, undefined) {

    "use strict";

    /**
     * Ajax-enabled forms public bootstraper
     */
    var SearchInitializer = namespace("Tmt.Initializers").SearchInitializer = function () {
        this.initialize();
    };

    inherit(['Tmt.EventEmitter'], SearchInitializer, {
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

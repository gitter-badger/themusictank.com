(function (undefined) {

    "use strict";

    var App = namespace("Tmt").App = function() {
        this.profile = null;
        this.initializers = [];
        this.events = [
            "ready"
        ];
    };

    inherit([ Evemit ], App, {
        'init': function (userdata) {
            this.profile = new Tmt.Models.Profile(userdata);
            prepareInitializers.call(this);
            this.emit("ready");
        }
    });

    function prepareInitializers() {
        // Create an intance of each initializer.
        for(var type in Tmt.Initializers) {
            this.initializers[type] = new Tmt.Initializers[type]();
        }

        // Run the initialization. This is done in two steps because
        // initializers may depend on one another.
        for(var type in this.initializers) {
            this.initializers[type].build(this);
        }
    }

}());

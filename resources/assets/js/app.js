(function (undefined) {
    "use strict";

    var App = namespace("Tmt").App = function App() {
        this.profile = null;
        this.initializers = [];
    };

    App.prototype = extend([ Evemit ], {

        'init': function (userdata) {
            this.profile = new tmt.Profile(userdata);
            prepareInitializers.bind(this);
            this.emit("init");
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
            this.initializers[type].build(app);
        }

    }

}());

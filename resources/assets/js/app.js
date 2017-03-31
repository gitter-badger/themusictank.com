(function (undefined) {

    "use strict";

    /**
     * The global wrapper for the application instance.
     * @namespace Tmt.App
     * @extends {Tmt.EventEmitter}
     * @property {Tmt.Models.Profile} profile - An active session profile
     * @property {Array} initializers - An array of Tmt.Initializers object.
     */
    var App = namespace("Tmt").App = function () {
        this.profile = null;
        this.initializers = [];

        this.initialize();
    };

    inherit([Tmt.EventEmitter], App, {

        /**
         * Boots the application instance
         * @public
         * @method
         */
        boot: function () {
            this.profile = new Tmt.Models.Profile();
            prepareInitializers.call(this);
            this.emit("ready");
        },

        /**
         * Assigns session data from PHP to this javascript
         * session instance.
         * @method
         * @public
         */
        setData: function (data) {
            if (data.profile) {
                this.profile.setData(data.profile);
            }
        }
    });

    /**
     * Loads all initializer objects that it can dynamically find
     * in the Tmt.Initializers namespace and then builds them.
     * @method
     * @private
     */
    function prepareInitializers() {
        // Create an intance of each initializer.
        for (var type in Tmt.Initializers) {
            this.initializers[type] = new Tmt.Initializers[type]();
        }

        // Run the initialization. This is done in two steps because
        // initializers may depend on one another.
        for (var type in this.initializers) {
            this.initializers[type].build(this);
        }
    }

}());

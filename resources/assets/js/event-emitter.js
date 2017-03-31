(function (undefined) {

    "use strict";

    /**
     * @namespace Tmt.EventEmitter
     * @property {array} events A collection of object events and callbacks.
     */
    var EventEmitter = namespace("Tmt").EventEmitter = function() {
        this.events = null;
    };

    inherit([Evemit], namespace("Tmt").EventEmitter, {

        /**
         * Initializes the event emitter object.
         * @method
         * @public
         */
        'initialize': function () {
            // Exposing the creation in a prototype method
            // ensures child classes will have an instantiated value
            // even if they don't go through the constructor.
            this.events = {};
        }
    });

}());

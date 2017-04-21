(function ($, undefined) {

    "use strict";

    var Renderer = namespace("Tmt.Components").Renderer = function () {
        this.canvas = null;
    };

    inherit([], Renderer, {

        linkTo: function (canvas) {
            this.canvas = canvas;
        },

        height: function () {
            return this.canvas.node.height;
        },

        width: function () {
            return this.canvas.node.width;
        },

        context: function () {
            return this.canvas.context;;
        },

        render: function () {
            throw new Error("MissingOverride");
        }

    });

})(jQuery);

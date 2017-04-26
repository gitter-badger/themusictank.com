(function ($, undefined) {

    "use strict";

    var Vector = namespace("Tmt.Components.Canvas").Vector = function (x, y) {
        this.y = y;
        this.x = x;
    };

    inherit([], Vector, {

        add : function (vector) {
            this.x = this.x + vector.x;
            this.y = this.y + vector.y;
        }

    });

})(jQuery);

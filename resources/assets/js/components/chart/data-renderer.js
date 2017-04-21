(function ($, undefined) {

    "use strict";

    var DataRenderer = namespace("Tmt.Components.Chart").DataRenderer = function (data) {
        this.data = data;
    };

    inherit([Tmt.Components.Renderer], UiRenderer, {
        render: function () {
            drawRange.call(this);
            drawJoins.call(this);
            drawDots.call(this);
        }
    });

    function drawRange() {

    }

    function drawJoins() {
    
        var height = this.height(),
            width = this.width(),
            context = this.context();


            console.log(data);

    }

    function drawDots() {

    }


})(jQuery);

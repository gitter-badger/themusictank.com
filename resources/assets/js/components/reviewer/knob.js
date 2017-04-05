(function ($, undefined) {

    "use strict";

    var track = null,
        knob = null,
        enabled = false,
        draggable = null;

    var Knob = namespace("Tmt.Components.Reviewer").Knob = function (element) {
        track = element;
        knob = element.find('b');
        enabled = false;

        addEvents();
    };

    inherit([], Knob, {
        enable: function () {
            track.removeClass("disabled");
            track.addClass("enabled");

            draggable.enable();

            enabled = true;
        },

        disable: function () {
            track.addClass("disabled");
            track.removeClass("enabled");

            draggable.disable();

            enabled = false;
        },

        setValue: function (value) {
            knob.css("top", (value * 100) + "%");
        }
    });

    function addEvents() {
        draggable = Draggable.create(knob.get(0), {
            type: "y",
            bounds: track.get(0)
        })[0];
    }


})(jQuery);

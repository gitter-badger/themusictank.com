(function ($, undefined) {

    "use strict";

    var Knob = namespace("Tmt.Components.Reviewer").Knob = function (element) {
        this.track = element;
        this.knob = element.find('b');

        this.enabled = false;
        this.working = false;
        this.position = null;
        this.trackHeight = null;
        this.draggable = null;
        this.nudged = false;

        addEvents.call(this);
        saveCurrentPosition.call(this);
    };

    inherit([], Knob, {
        enable: function () {
            this.track.removeClass("disabled");
            this.track.addClass("enabled");

            this.draggable.enable();
            this.enabled = true;
        },

        disable: function () {
            this.track.addClass("disabled");
            this.track.removeClass("enabled");

            this.draggable.disable();
            this.enabled = false;
        },

        setValue: function (value) {
            var topPosition = this.trackHeight * (1 - value);
            TweenMax.set(this.knob.get(0), { css: { y:  topPosition } });
            this.draggable.update();
        },

        getValue: function () {
            var value = 1 - (this.draggable.y / this.trackHeight);

            // Ensure we don't break boundries
            if (value > 1)  {
                return 1;
            } else if (value < 0) {
                return 0;
            }

            return value;
        },

        isWorking : function() {
            return this.working;
        },

        isEnabled : function() {
            return this.enabled;
        },

        stopCurrentDrag : function() {
            this.draggable.disable();
            this.draggable.enable();
        },

        nudge : function() {
            this.nudged = true;
            this.track.css({
                'margin-top' : (Math.random() <= 0.5 ?  2 : -2) + "px",
                'margin-left' : (Math.random() <= 0.5 ?  2 : -2) + "px"
            });
        },

        center : function() {
            if (this.nudged) {
                this.track.css({
                    'margin-top' : null,
                    'margin-left' : null
                });
            }
        }
    });

    function saveCurrentPosition() {
        this.position = this.track.position();
        this.trackHeight = this.track.innerHeight() - this.knob.outerHeight();
    }

    function addEvents() {
        this.draggable = Draggable.create(this.knob.get(0), {
            type: "y",
            bounds: this.track.get(0),
            onDragStart: onDragStart.bind(this),
            onDragEnd: onDragEnd.bind(this),
        })[0];
    }

    function onDragStart() {
        this.working = true;
    }

    function onDragEnd() {
        this.working = false;
    }

})(jQuery);

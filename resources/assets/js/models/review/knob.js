export default class Knob {

    constructor(element) {
        this.track = element;
        this.knob = element.find('b');

        this.enabled = false;
        this.working = false;
        this.position = null;
        this.trackHeight = null;
        this.draggable = null;
        this.nudged = false;

        this.value = 0;

        addEvents.call(this);
        saveCurrentPosition.call(this);
    }

    enable() {
        this.track.removeClass("disabled");
        this.track.addClass("enabled");

        this.draggable.enable();
        this.enabled = true;
    }

    disable() {
        this.track.addClass("disabled");
        this.track.removeClass("enabled");

        this.draggable.disable();
        this.enabled = false;
    }

    setValue(value) {
        this.value = value;

        if (!this.working) {
            var topPosition = this.trackHeight * (1 - value);
            TweenMax.set(this.knob.get(0), { css: { y: topPosition } });
            this.draggable.update();
        }
    },

    getValue() {
        if (this.working) {
            var value = 1 - (this.draggable.y / this.trackHeight);
        } else {
            var value = this.value;
        }

        // Ensure we don't break boundries
        if (value > 1) {
            return 1;
        } else if (value < 0) {
            return 0;
        }

        return value;
    }

    isWorking() {
        return this.working;
    }

    isEnabled() {
        return this.enabled;
    }

    stopCurrentDrag() {
        this.draggable.disable();
        this.draggable.enable();
    }

    nudge() {
        this.nudged = true;
        this.track.css({
            'margin-top': (Math.random() <= 0.5 ? 2 : -2) + "px",
            'margin-left': (Math.random() <= 0.5 ? 2 : -2) + "px"
        });
    }

    center() {
        if (this.nudged) {
            this.track.css({
                'margin-top': null,
                'margin-left': null
            });
        }
    }
}

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

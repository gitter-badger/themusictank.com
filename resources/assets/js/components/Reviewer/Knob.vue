<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: ['enabled', 'reviewer'],
    data() {
        return {
            'dragging' : false,
            'draggable' : null,
            'nudged' : false,
            'value' : 0
        }
    },
    computed : {
        track() {
            return this.getElement();
        },
        knob() {
            return this.track.find('b');
        },
        position() {
            return this.track.position();
        },
        trackHeight() {
            return this.track.innerHeight() - this.knob.outerHeight();
        },
        valueFromPosition() {
            let value = 1 - (this.draggable.y / this.trackHeight);

            // Ensure we don't break boundries
            if (value > 1)  {
                return 1;
            } else if (value < 0) {
                return 0;
            }

            return value;
        }
    },
    watch: {
        value(val) {
            if (!this.dragging) {
                let topPosition = this.trackHeight * (1 - val);
                TweenMax.set(this.knob.get(0), { css: { y:  topPosition } });
                this.draggable.update();
            }
        }
    },
    mounted() {
        this.draggable = Draggable.create(this.knob.get(0), {
            type: "y",
            bounds: this.track.get(0),
            onDragStart: () => { this.dragging = true },
            onDragEnd: () => { this.dragging = false },
        })[0];
    },
    methods : {

        isDragging() {
            return this.dragging;
        },

        stopCurrentDrag() {
            this.draggable.disable();
            this.draggable.enable();
        },

        nudge() {
            this.nudged = true;
            this.track.css({
                'margin-top': (Math.random() <= 0.5 ? 2 : -2) + "px",
                'margin-left': (Math.random() <= 0.5 ? 2 : -2) + "px"
            });
        },

        center() {
            if (this.nudged) {
                this.track.css({
                    'margin-top': null,
                    'margin-left': null
                });
            }
        },
    }
};
</script>

<template>
    <span
        class="knob-track"
        :class="{
            enabled: enabled,
            dragging: isDragging
        }"
    ><b></b></span>
</template>

<style lang="scss">
.knob-track {
    position: absolute;
    top: 50%;
    left: 50%;
    max-width: 75px;
    width: 10%;
    height: 80%;
    max-height: 450px;
    background: #111;
    border: 1px solid #333;
    display: block;
    transform: translate(-50%, -50%);
    overflow: hidden;
    z-index: 2;
    border-radius: 72px;

    b {
        transition: transform .3s;
        background: #666;
        border: 1px solid #333;
        box-shadow: 0 2px 7px #000;
        display: block;
        border-radius: 72px;
        width: 72px;
        height: 72px;
        position: absolute;
    }

    &.dragging b {
        transition: none;
    }

    &.enabled b {
        background: #eee;
        cursor: pointer;
    }
}
</style>

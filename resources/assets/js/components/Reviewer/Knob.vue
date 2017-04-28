<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: ['enabled'],
    data() {
        return {
            'dragging': false,
            'draggable': null
        }
    },
    computed : {
        trackHeight() {
            return this.getElement().height();
        },
        handle() {
            return this.getElement().find("b").get(0);
        },
        value() {
            if (this.enabled && this.draggable) {
                var value = 1 - (this.draggable.y / this.trackHeight);
            } else {
                var value = this.value;
            }

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
                this.applyValue();
            }
        },
        enabled(val) {
            if (this.draggable) {
                val ? this.draggable.enable() : this.draggable.disable();
            }
        }
    },
    mounted() {
         this.draggable = Draggable.create(this.handle, {
            type: "y",
            bounds: this.$el,
            onDragStart: () => { this.dragging = true },
            onDragEnd: () => { this.dragging = false }
        })[0];
    },
    methods: {
        applyValue() {
            var topPosition = this.trackHeight * (1 - val);
            TweenMax.set(this.handle, { css: { y:  topPosition } });
            this.draggable.update();
        }
    }

};
</script>

<template>
    <span
        class="knob-track"
        :class="{enabled: enabled}"
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


    &.enabled b {
        background: #eee;
        cursor: pointer;
    }
}
</style>

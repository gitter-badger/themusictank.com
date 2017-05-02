<script>
import $ from 'jquery'
import ComponentBase from '../mixins/base.js'
import Knob from './Knob.vue'

import ReviewerModel from '../../models/review/reviewer.js'

import Canvas from '../../models/canvas/canvas.js'
import Vector from '../../models/canvas/vector.js'
import ParticleEmitter from '../../models/canvas/emitter/particle-emitter.js'

export default {
    mixins: [ComponentBase],
    components: {
        Knob
    },
    props: [
        'songName',
        'songSlug',
        'profileSlug',
        'albumName',
        'enabled',
        'position',
        'completed'
    ],
    watch: {
        enabled(val) {
            if (this.reviewer) {
                val ? this.reviewer.start() : this.reviewer.stop();
            }

            if (this.canvas) {
                val ? this.canvas.start() : this.canvas.stop();
            }
        },
        isPowersucking(val) {
            if (this.canvas && val) {
                this.canvas.emit("negativeSpark", 200);
            }
        },
        isPowerliking(val) {
            if (this.canvas && val) {
                this.canvas.emit("positiveSpark", 200);
            }
        },
        isShaking(val) {
            if (val && this.canvas && this.reviewer) {
                this.reviewer.isPositive() ?
                    this.canvas.emit("positiveSpark", 10) :
                    this.canvas.emit("negativeSpark", 10);
            }
        }
    },
    data() {
        return {
            'reviewer': null,
            'canvas': null,
            'synchronising': false,
            'syncComplete': false,
        }
    },
    computed: {
        isPowersucking() {
            return this.reviewer && this.reviewer.isPowersucking;
        },

        isPowerliking() {
            return this.reviewer && this.reviewer.isPowerliking;
        },

        isShaking() {
            return this.reviewer && this.reviewer.shaking;
        },

        height() {
            return this.getElement().height();
        },

        width() {
            return this.getElement().width();
        }
    },
    mounted() {
        this.reviewer = new ReviewerModel(this, this.$refs.knob);

        let canvasTag = this.getElement().find('canvas');
        this.canvas = new Canvas(canvasTag);
        this.canvas.resize(this.width, this.height);

        let position = new Vector(this.width / 2, this.height * .15);
        this.canvas.addEmitter("positiveSpark", new ParticleEmitter(position));

        position = new Vector(this.width / 2, this.height * .85);
        this.canvas.addEmitter("negativeSpark", new ParticleEmitter(position));

        $(window).on('resize', this.debounce(() => {
            this.canvas.resize(this.width, this.height);
            this.canvas.draw();
        }));

        this.reviewer.start();
        this.canvas.start();
    },
    methods: {
        sendFramesPackage(span) {
            this.synchronising = true;
            let payload = { 'package': span };

            this.ajax()
                .post("/ajax/" + this.songSlug + "/saveCurvePart/", payload)
                .then((response) => {
                    this.synchronising = false;

                    if (this.completed) {
                        if (this.reviewer.hasUnsynchronisedFrames()) {
                            this.reviewer.save();
                        } else {
                            this.syncComplete = true;
                        }
                    }
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.synchronising = false;
                });
        }
    }
};
</script>

<template>
    <div class="ctrl ctrl-reviewer">
        <canvas></canvas>
        <knob ref="knob" :enabled="enabled"></knob>
        <completed-dialogs v-if="completed" :sync-complete="syncComplete" :profile-slug="profileSlug" :song-name="songName" :album-name="albumName"></completed-dialogs>
        <i v-if="synchronising" class="synchronising fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>
    </div>
</template>


<style lang="scss">
.ctrl-player.has-reviewer {
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    height: auto;
    z-index: 2;
    background: #fff;

    .ui {
        position: absolute;
        bottom: 70px;
        left: 0;
        right: 0;
        background: transparent;
        border: none;
        z-index: 2;
    }
}

.ctrl-reviewer {
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    z-index: 2;

    canvas {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1;
    }

    i {
        position: absolute;
        bottom: 90px;
        right: 20px;
        z-index: 2;
    }

    .times {
        color: #333;
    }
}
</style>

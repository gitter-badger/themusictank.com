(function ($, undefined) {

    "use strict";

    var Particle = Tmt.Components.Reviewer.Emitter.Particle,
        Vector = Tmt.Components.Reviewer.Emitter.Vector;

    var ParticleEmitter = namespace("Tmt.Components.Reviewer.Emitter").ParticleEmitter = function () {
        this.particles = [];
        this.position = new Vector(0, 0);
    };

    inherit([], ParticleEmitter, {

        isRunning: function () {
            return this.particles.length > 0;
        },

        setCanvas: function (canvas) {
            this.canvas = canvas;
        },

        moveTo: function (x, y) {
            this.position = new Vector(x, y);
        },

        start: function (quantity) {
            for (var i = 0; i < quantity; i++) {
                this.particles.push(new Particle(this.canvas, this.position.x, this.position.y));
            }
        },

        run: function () {
            for (var i = this.particles.length - 1; i >= 0; i--) {
                this.particles[i].update();
                if (this.particles[i].isDead()) {
                    this.particles.pop();
                }
            }
        },

        render: function () {
            for (var i = this.particles.length - 1; i >= 0; i--) {
                this.particles[i].paint();
            }
        }

    });


})(jQuery);

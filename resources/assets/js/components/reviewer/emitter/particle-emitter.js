(function ($, undefined) {

    "use strict";

    var Particle = Tmt.Components.Reviewer.Emitter.Particle;

    var ParticleEmitter = namespace("Tmt.Components.Reviewer.Emitter").ParticleEmitter = function (element) {
        this.rootNode = element;
        this.x = 0;
        this.y = 0;
        this.particles = [];
    };

    inherit([], ParticleEmitter, {

        moveTo : function (x, y) {
            this.x = x;
            this.y = y;
        },

        initialize: function (quantity) {
            for (var i = 0; i < quantity; i++) {
                this.particles[i] = new Particle(0, 0, this.rootNode);
            }
        },

        tick: function () {
            for (var i = 0; i < this.particles.length; i++) {
                this.particles[i].update();
            }
        },

        animate: function () {
            for (var i = this.particles.length - 1; i >= 0; i--) {
                if (this.particles[i].isDead()) {
                    var particle = this.particles.pop();
                    particle.remove();
                } else {
                    this.particles[i].paint();
                }
            }
        }

    });


})(jQuery);

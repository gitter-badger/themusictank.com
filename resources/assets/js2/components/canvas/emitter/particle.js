(function ($, undefined) {

    "use strict";

    var Vector = Tmt.Components.Canvas.Vector;

    var Particle = namespace("Tmt.Components.Canvas.Emitter").Particle = function (canvas, x, y) {
        this.size = Math.random() * 10 + 15;

        this.position = new Vector(x, y);
        var velocityX = (Math.random() * 5) * (Math.random() >= 0.5 ? 1 : -1);
        var velocityY = Math.random() * 5;

        this.velocity = new Vector(velocityX, velocityY);
        this.acceleration = new Vector(0, 0.1);
        this.lifespan = Math.random() * 350;

        this.image = new Image();
        this.image.src = '/img/spark.png';
        this.context = canvas.getContext('2d');

        this.image.onload = function () {
            this.context.drawImage(this.image, this.position.x, this.position.y);
        }.bind(this);
    };

    inherit([], Particle, {

        update: function () {
            this.velocity.add(this.acceleration);
            this.position.add(this.velocity);
            this.lifespan -= 1;
        },

        isDead: function () {
            return this.lifespan < 0;
        },

        paint: function () {
            this.context.save();

            if (this.lifespan < 100) {
                this.context.globalAlpha = this.lifespan / 100;
            }

            this.context.drawImage(this.image, this.position.x, this.position.y);
            this.context.restore();
        }

    });


})(jQuery);

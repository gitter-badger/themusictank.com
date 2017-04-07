(function ($, undefined) {

    "use strict";

    var Vector = Tmt.Components.Reviewer.Emitter.Vector;

    var Particle = namespace("Tmt.Components.Reviewer.Emitter").Particle = function (x, y, attachTo) {
        this.size = Math.random() * 10 + 15;

        this.position = new Vector(x, y);
        var velocityX = (Math.random() * 3) * (Math.random() >= 0.5 ? 1 : -1);
        var velocityY = Math.random() * 5;

        this.velocity = new Vector(velocityX, velocityY);
        this.acceleration = new Vector(0, 0.085);
        this.lifespan = Math.random() * 650;

        this.image  = $('<img src="/assets/img/spark.png">');
        this.image.css({
            top : 0,
            left : 0
        });
        attachTo.append(this.image);
    };

    inherit([], Particle, {

        update : function() {
            this.velocity.add(this.acceleration);
            this.position.add(this.velocity);
            this.lifespan -= 1;
        },

        isDead : function(){
            return this.lifespan < 0;
        },

        paint : function() {
            var offset = this.image.offset();
            this.image.css({
                top : this.position.y,
                left : this.position.x
            })

            if (this.lifespan < 100) {
                this.image.fadeOut();
            }

        },

        remove : function() {
			this.image.remove();
        }

    });


})(jQuery);

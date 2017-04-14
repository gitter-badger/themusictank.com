(function ($, undefined) {

    "use strict";

    var Canvas = namespace("Tmt.Components").Canvas = function (element) {
        this.rootNode = element;
        this.node = element.get(0);
        this.context = this.node.getContext('2d');
        this.emitters = {};

        addEvents.call(this);
    };

    inherit([], Canvas, {

        addEmitter : function(id, x, y) {
            var emitter = new Tmt.Components.Reviewer.Emitter.ParticleEmitter();
            emitter.setCanvas(this.node);
            emitter.moveTo(x, y);

            this.emitters[id] = emitter;
        },

        emit : function(id, qty) {
            this.emitters[id].start(qty);
        },

        draw : function() {
            this.context.clearRect(0, 0, this.node.width, this.node.height);
            for(var i in  this.emitters) {
                if (this.emitters[i].isRunning()) {
                    this.emitters[i].run();
                    this.emitters[i].render();
                }
            }
        }

    });

    function addEvents () {
        $(window).on('resize', debounce(applyCurrentSize.bind(this)));
        applyCurrentSize.call(this);
    }

    function applyCurrentSize () {
        this.node.height = this.rootNode.parent().height();
        this.node.width = this.rootNode.parent().width();
    }


})(jQuery);

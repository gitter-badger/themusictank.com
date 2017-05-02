export default class Canvas {

    constructor(element) {
        this.rootNode = element;
        this.node = element.get(0);

        this.context = this.node.getContext('2d');
        this.emitters = {};
        this.renderers = [];
        this.animating = false;
        this.drawnFrameId = 0;
        this.currentFrameId = 0;
    }

    start() {
        this.animating = true;
        this.animate();
    }

    stop() {
        this.animating = false;
    }

    animate() {
        setFrameContext.call(this);

        if (this.animating) {
            if (this.drawnFrameId != this.currentFrameId) {
                this.drawnFrameId = this.currentFrameId;
                this.draw();
            }
            requestAnimationFrame( () => { this.animate(); });
        }
    }

    addEmitter(id, emitter) {
        emitter.setCanvas(this.node);
        this.emitters[id] = emitter;
    }

    addRenderer(renderer) {
        this.renderers.push(renderer);
        renderer.linkTo(this);
    }

    emit(id, qty) {
        this.emitters[id].start(qty);
    }

    draw() {
        this.context.clearRect(0, 0, this.node.width, this.node.height);

        for (var i in this.renderers) {
            this.renderers[i].render();
        }

        for (var i in this.emitters) {
            if (this.emitters[i].isRunning()) {
                this.emitters[i].run();
                this.emitters[i].render();
            }
        }
    }

    resize(width, height) {
        this.node.height = height;
        this.node.width = width;
    }
};

function setFrameContext() {
    this.currentFrameId++;

    if (this.currentFrameId > 100000) {
        this.currentFrameId = 1;
    }
}

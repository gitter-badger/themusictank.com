import Vector from '../vector.js'
import Particle from './particle.js'

export default class ParticleEmitter {

    constructor(position) {
        this.particles = [];
        this.position = new Vector(0, 0);

        if (position) {
            this.moveTo(position.x, position.y);
        }
    }

    isRunning() {
        return this.particles.length > 0;
    }

    setCanvas(canvas) {
        this.canvas = canvas;
    }

    moveTo(x, y) {
        this.position = new Vector(x, y);
    }

    start(quantity) {
        for (var i = 0; i < quantity; i++) {
            this.particles.push(new Particle(this.canvas, this.position.x, this.position.y));
        }
    }

    run() {
        for (var i = this.particles.length - 1; i >= 0; i--) {
            this.particles[i].update();
            if (this.particles[i].isDead()) {
                this.particles.pop();
            }
        }
    }

    render() {
        for (var i = this.particles.length - 1; i >= 0; i--) {
            this.particles[i].paint();
        }
    }
}


export default class Reviewer {

    constructor() {
        this.timers = {
            highGrooveStart: null,
            lowGrooveStart: null,
        };

        this.shaking = false;
        this.synchronising = false;

        this.currentFrameId = 0;
        this.drawnFrameId = null;
        this.savedFrameIdx = 0;

        this.currentGroove = 0;
        this.grooveCurve = [];
    }

};

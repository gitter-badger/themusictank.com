import Frame from './frame'

export default class ReviewFrames {
    constructor(frameData) {
        this.cache = [];

        frameData.forEach(frameData => {
            this.cache.push = new Frame(frameData)
        })
    }
};
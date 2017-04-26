
export default class ReviewFrames {
    constructor(frameData) {
        this.position = frameData.position;
        this.avg_groove = frameData.avg_groove;
        this.high_avg_groove = frameData.high_avg_groove;
        this.low_avg_groove = frameData.low_avg_groove;
        
        // @todo: I don't think we care about these values and
        // maybe they should not be sent through php in the first place
        this.id = frameData.id;
        this.profileId = frameData.profileId;
        this.trackId = frameData.trackId;
    }
};
import Frame from './frame.js'

export default class Track {
    constructor(id, frameData) {
        this.id = id;
        this.global = [];
        this.tanker = [];
        this.subscriptions = [];
    }

    setData(source, data) {
        if (!data) {
            data = []
        }

        data.forEach(frameData => {
            this[source].push(new Frame(frameData));
        });
    }

    getSource(source) {
        return this[source];
    }

};

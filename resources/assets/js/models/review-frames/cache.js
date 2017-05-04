import Track from './track.js'

export default class Cache {
    constructor() {
        this.tracks = [];
    }

    addTrack(data) {
        let track = new Track(data['id']);
        track.setData('global', data['global']);
        track.setData('tanker', data['tanker']);
        track.setData('subscriptions', data['subscriptions']);

        this.tracks.push(track);
    }

    getTrack(id) {
        return this.tracks.find(cache => { return parseInt(cache.id, 10) == parseInt(id, 10) });
    }

    getTrackData(id, source) {
        let track = this.getTrack(id);
        return track.getSource(source);
    }
};

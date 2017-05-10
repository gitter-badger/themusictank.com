import _ from 'lodash';

export default class UpvoteCache {
    constructor(userData) {
        this.albumUpvotes = userData.albumUpvotes || [];
        this.trackUpvotes = userData.trackUpvotes || [];
    }

    /**
     * Add a new album vote
     * @param {string} key album id
     * @param {string} value
     * @public
     * @method
     */
    addAlbumUpvote (key, value) {
        this.albumUpvotes.push({'album_id': parseInt(key, 10), 'vote': parseInt(value, 10)});
    }

    /**
     * Add a new track vote
     * @param {string} key track id
     * @param {string} value
     * @public
     * @method
     */
    addTrackUpvote (key, value) {
        this.trackUpvotes.push({'track_id': parseInt(key, 10), 'vote': parseInt(value, 10)});
    }

    /**
     * Removes an existing album vote
     * @param {string} id album id
     * @param {string} value
     * @public
     * @method
     */
    removeAlbumUpvote (id) {
        let index = this.albumUpvotes.map(function(vote) {return vote.album_id; }).indexOf(parseInt(id, 10));
        if (index > -1) {
            this.albumUpvotes.splice(index, 1);
        }
    }

    /**
     * Removes an existing track vote
     * @param {string} id track id
     * @param {string} value
     * @public
     * @method
     */
    removeTrackUpvote (id) {
        let index = this.trackUpvotes.map(function(vote) {return vote.track_id; }).indexOf(parseInt(id, 10));
        if (index > -1) {
            this.trackUpvotes.splice(index, 1);
        }
    }

    findByTrackId(id) {
        let vote = this.trackUpvotes.find(vote => { return parseInt(vote.track_id, 10) == parseInt(id, 10) });
        if (vote) {
            return vote.vote;
        }
    }

    findByAblumId(id) {
        let vote = this.albumUpvotes.find(vote => { return parseInt(vote.album_id, 10) == parseInt(id, 10) });
        if (vote) {
            return vote.vote;
        }
    }

    getVote(type, id) {
        return type == "track" ?
            this.findByTrackId(id) :
            this.findByAblumId(id);
    }
}

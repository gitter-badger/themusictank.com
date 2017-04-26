import _ from 'lodash';
import Vuex from 'vuex';
import {mapGetters} from 'vuex';


export default class Profile {
    constructor(userData, upvoteCache) {
        this.username = userData.username;
        this.email = userData.email;
        this.slug = userData.slug;
        this.name = userData.name;
        this.id = userData.id;
    }

    /**
     * Adds a user activity notification (viewed or not)
     * @param {hash} notification
     * @public
     * @method
     */
    addNotification (notification) {
        this.notifications.push(notification);

        if (this.notifications.length > 10) {
            this.notifications.length = 10;
        }
    }

    getVoteByObjectId (type, objectId) {
        let data = type == "track" ? this.trackUpvotes : this.albumUpvotes,
            match = _.filter(data, {id: objectId});

        if (match) {
            return match.vote;
        }
    }
}

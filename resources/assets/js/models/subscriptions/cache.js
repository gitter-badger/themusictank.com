import Sub from './sub.js'

export default class Cache {
    constructor() {
        this.subscriptions = [];
    }

    addUser(data) {
        this.subscriptions.push(new Sub(data));
    }

    removeUser(subId) {
        this.subscriptions = this.subscriptions.filter(cache => {
            return parseInt(cache.sub_id, 10) != parseInt(subId, 10);
        });
    }

    count() {
        return this.subscriptions.length;
    }

    getAll() {
        return this.subscriptions;
    }

    subscribed(subId) {
        return this.subscriptions.find(cache => {
            return parseInt(cache.sub_id, 10) == parseInt(subId, 10)
        });
    }

};

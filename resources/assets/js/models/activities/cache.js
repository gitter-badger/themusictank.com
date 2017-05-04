import Activity from './activity.js'

export default class Cache {
    constructor() {
        this.activities = [];
    }

    addActivity(data) {
        this.activities.push(new Activity(data));
    }

    getNew() {
        return this.activities.filter(cache => { return cache.must_notify; });
    }

    count() {
        return this.activities.length;
    }

    getAll() {
        return this.activities;
    }
};

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

    findById(id) {
        for (var i = 0, len = this.count(); i < len; i++) {
            if (this.activities[i].id == id) {
                return this.activities[i];
            }
        }
    }
};

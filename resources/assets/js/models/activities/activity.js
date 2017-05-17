
export default class Activity {
    constructor(frameData) {
        this.id = frameData.id;
        this.associated_object_id = parseInt(frameData.associated_object_id, 10);
        this.associated_object_type = parseInt(frameData.associated_object_type, 10);
        this.must_notify = frameData.must_notify;
        this.updated_at = frameData.updated_at;
        this.created_at = frameData.created_at;
        this.associated_object = frameData.associated_object;
    }

    getLabel() {
        if (this.associated_object_type === 1) {
            return 'You have earned the achievement: ' + this.associated_object.name + '.';
        }

        if (this.associated_object_type === 2) {
            return this.associated_object.name + ' is now following you.';
        }
    }

    getLink() {
        if (this.associated_object_type === "profile") {
            return "/tankers/" + this.associated_object.slug;
        }
    }
};

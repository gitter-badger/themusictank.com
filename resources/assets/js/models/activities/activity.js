
export default class Activity {
    constructor(frameData) {
        this.id = frameData.id;
        this.associated_object_id = frameData.associated_object_id;
        this.associated_object_type = frameData.associated_object_type;
        this.must_notify = frameData.must_notify;
        this.updated_at = frameData.updated_at;
        this.created_at = frameData.created_at;
        this.associated_object = frameData.associated_object;
    }

    getLabel() {
        if (this.associated_object_type === "profile") {
            return this.associated_object.name + ' is now following you.';
        }
    }

    getLink() {
        if (this.associated_object_type === "profile") {
            return "/tankers/" + this.associated_object.slug;
        }
    }
};

describe("models/profile.js", function () {

    var profile;

    beforeEach(function(){
        profile = new Tmt.Models.Profile();
    });

    it("can be configured", function () {
        profile.setData({
            'username': 'test',
            'email': 'test',
            'slug': 'test',
            'name': 'test',
            'id': 'test',
            'albumUpvotes': [],
            'trackUpvotes': []
        });

        expect(profile.username).toBe("test");
        expect(profile.email).toBe("test");
        expect(profile.slug).toBe("test");
        expect(profile.name).toBe("test");
        expect(profile.id).toBe("test");
        expect(profile.albumUpvotes).toEqual([]);
        expect(profile.trackUpvotes).toEqual([]);
    });

    it("triggers dataChange event", function () {
        var spy = jasmine.createSpy("onDataChange", function () { }).and.callThrough();

        profile.on('dataChange', spy);

        profile.setData({ 'username': 'test' });
        expect(spy).toHaveBeenCalled();

        var eventProfile = spy.calls.mostRecent().args[0];
        expect(eventProfile.username).toBe("test");
    });

    it("can add album upvotes", function () {
        profile.setData({});
        profile.addUpvote("album", 12, 2);
        expect(profile.albumUpvotes).toEqual({'12': {'id': 12, 'vote': 2}});
    });

    it("can add track upvotes", function () {
        profile.setData({});
        profile.addUpvote("track", 12, 2);
        expect(profile.trackUpvotes).toEqual({'12': {'id': 12, 'vote': 2}});
    });

    it("can remove album upvotes", function () {
        profile.setData({});
        profile.removeUpvote("album", 12);
        expect(profile.albumUpvotes).toEqual({ });
    });

    it("can remove track upvotes", function () {
        profile.setData({});
        profile.removeUpvote("track", 12);
        expect(profile.trackUpvotes).toEqual({ });
    });

    it("can retreive upvotes", function () {
        profile.setData({});
        profile.addUpvote("track", 12, 2);
        profile.addUpvote("album", 12, 2);

        expect(profile.getVoteByObjectId("track", 12)).toEqual(2);
        expect(profile.getVoteByObjectId("album", 12)).toEqual(2);
    });


});

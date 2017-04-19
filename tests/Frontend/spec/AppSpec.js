describe("app.js", function () {

    it("should boot with the expected default values", function () {
        var app = new Tmt.App();
        app.boot();
        expect(typeof(app.profile)).toBe('object');
    });


});

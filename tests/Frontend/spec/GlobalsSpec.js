describe("globals.js", function () {

    it("should set a default Ajax forgery token", function () {
        expect(jQuery.ajaxSettings.headers["X-CSRF-TOKEN"]).toBe("CSRFTOKENVALUE");
    });

    describe("namespace()", function () {
        it("should create a deep namespace object", function () {
            namespace("here.is.a").Test = function () { };
            expect(here.is.a.Test).toBeDefined();
        });
    });

    describe("extend()", function () {
        it("should extend an object", function () {
            var left = { "foo": "bar" },
                right = { "zee": "zoo" },
                extented = extend(left, right);

            expect(extented).toEqual({
                "foo": "bar",
                "zee": "zoo"
            });
        });

        it("should not overwrite existing properties", function () {
            var left = { "foo": "bar" },
                right = { "foo": "dee", "zee": "zoo" },
                extented = extend(left, right);

            expect(extented).toEqual({
                "foo": "dee",
                "zee": "zoo"
            });
        });
    });

    describe("inherit()", function () {
        it("should create prototype inheritance", function () {

            var Parent = function () { };
            Parent.prototype.superFn = function () {
                return "parent superFn";
            };

            var Child = function () { };
            inherit([Parent], Child, {
                'childFn': function () {
                    return "child childFn";
                }
            });

            var test = new Child();
            expect(test.superFn()).toEqual("parent superFn");
            expect(test.childFn()).toEqual("child childFn");
        });

        it("should not overwrite child methods", function () {
            var Parent = function () { };
            Parent.prototype.superFn = function () {
                return "parent superFn";
            };

            var Child = function () { };
            inherit([Parent], Child, {
                'superFn': function () {
                    return "child superFn";
                }
            });

            var test = new Child();
            expect(test.superFn()).toEqual("child superFn");
        });
    });

    describe("filter()", function () {
        it("should filter matching objects", function () {
            var stacks = [{
                'getRootNode': function () {
                    return jQuery("<em>");
                }
            }, {
                'getRootNode': function () {
                    return jQuery("<em>");
                }
            }, {
                'getRootNode': function () {
                    return jQuery("<div><p><em></em></p></div><div><p><em></em></p></div>");
                }
            }];

            expect(filter("em", stacks).length).toBe(2);
        });
    });
});

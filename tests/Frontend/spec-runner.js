var phantom = require("phantom");
var _ph, _page, _outObj;

phantom.create().then(ph => {
    _ph = ph;
    return _ph.createPage();
}).then(page => {
    _page = page;
    return _page.open('file:///' + __dirname.replace(/\\/g, "/") + '/SpecRunner.html');
}).then(status => {
    return _page.evaluate(function () { return document.querySelector('.jasmine-bar.jasmine-passed'); });
}).then(element => {
    if (!element) {
        console.log(0);
    }
    return _page.close();
}).then(arg => {
    return _ph.exit();
}).catch(e => console.log(e));

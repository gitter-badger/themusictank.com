$(function() {

    // Scrolling effects
    // -----------------
    //
    // When a user scrolls or chages the width of the page, handle all the visuals
    //

    // Frequent calls of DOM objects should be cached here
    var _domcache = {
        "window" : $(window),
        ".navbar" : $('.navbar'),
        ".breadcrumbs" : $('.breadcrumbs'),
        ".header-wrapper" : $('.header-wrapper'),
        ".header-wrapper .mask" : $('.header-wrapper .mask'),
        ".header-wrapper div" : $('.header-wrapper div'),
        ".header-wrapper div.clean" : $('.header-wrapper div.clean')
    }

    // Improves the performance of scroll events.
    function _debounce(func, wait) {
        var timeout;
        return function() {
            var context = this,
                args = arguments,
                later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Handles the fixed navigation bar
    function _handleHeaderFX() {
        if(_domcache["window"].scrollTop() > 70) {
            _domcache['.navbar'].addClass('opaque');
            _domcache['.breadcrumbs'].addClass('opaque');
        } else {
            _domcache['.navbar'].removeClass('opaque');
            _domcache['.breadcrumbs'].removeClass('opaque');
        }
    }

    // Handle the blurring of the header image
    function _handleBackdropFX() {
        if(_domcache['.header-wrapper'].length) {
            var scrollTop = _domcache["window"].scrollTop(),
                scrolledDistance = scrollTop / 5,
                wrapHeight = _domcache['.header-wrapper'].innerHeight(),
                imgHeight = _domcache['.header-wrapper div.clean'].height(),
                opacityVal = 1 - (scrollTop / 150.0),
                threshold = 5;

            if(scrolledDistance > threshold && scrolledDistance < wrapHeight) {
                _domcache['.header-wrapper div.clean'].css("opacity", opacityVal);
                // Mask moves slower because it kewl as shiat
                _domcache['.header-wrapper .mask'].css('background-position-y', -(scrolledDistance * .3) +'px'  );
                // Parallax the image only if it's still big enough
                if(scrolledDistance < imgHeight - wrapHeight) {
                    _domcache['.header-wrapper div'].css('top', (-scrolledDistance) +'px'  );
                }
            }
            else if(scrolledDistance <= threshold) {
                _domcache['.header-wrapper .mask'].css('background-position-y', '0px');
                _domcache['.header-wrapper div'].css('top', '0px'  );
                _domcache['.header-wrapper div.clean'].css("opacity", 1)
            }
        }
    }

    // Events
    function _window_onScroll(event)
    {
        _handleHeaderFX();
        _handleBackdropFX();
    }

    // Attach the event on load
    _domcache['window'].scroll(_debounce(_window_onScroll, 60));

});

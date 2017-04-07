(function ($, undefined) {

    "use strict";

    var Canvas = namespace("Tmt.Components.Reviewer").Canvas = function (element) {
        this.rootNode = element;
        addEvents.call(this);
    };

    inherit([], Canvas, {


    });

    function addEvents () {
        $(window).on('resize', debounce(applyCurrentSize.bind(this)));
        applyCurrentSize.call(this);
    }

    function applyCurrentSize () {
        this.rootNode.get(0).height = this.rootNode.parent().height();
        this.rootNode.get(0).width = this.rootNode.parent().width();
    }


})(jQuery);

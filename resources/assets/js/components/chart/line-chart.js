(function ($, undefined) {

    "use strict";

    var LineChart = namespace("Tmt.Components.LineChart").LineChart = function (element, data) {
        this.rootNode = element;
        this.canvas = null;

        this.data = [];
        this.wave = [];
        this.start = 0;
        this.end = 0;
    };

    inherit([], LineChart, {

        setData: function (data) {
            this.data = data;
        },

        setWave: function (data) {
            this.wave = data;
        },

        setRange: function (start, end) {
            this.start = start;
            this.end = end;
        },

        build: function () {
            var canvasTag = $('<canvas>');
            this.rootNode.html(canvasTag);

            this.canvas = new Tmt.Components.Canvas(canvasTag);
            this.canvas.addRenderer(new Tmt.Components.Chart.UiRenderer());
            this.canvas.addRenderer(new Tmt.Components.Chart.DataRenderer(this.data));
        }

    });

})(jQuery);

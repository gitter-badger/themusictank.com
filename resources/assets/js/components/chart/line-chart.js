(function ($, undefined) {

    "use strict";

    var LineChart = namespace("Tmt.Components.Chart").LineChart = function (element, data) {
        this.rootNode = element;
        this.canvas = null;

        this.data = [];
        this.wave = [];
        this.start = 0;
        this.end = 0;
    };

    inherit([], LineChart, {

        render : function() {
            registerCanvas.call(this);
            drawWaves.call(this);
            drawChart.call(this);
        },

        setData : function(data) {
            this.data = data;
        },

        setWave : function(data) {
            this.wave = data;
        },

        setRange : function(start, end) {
            this.start = start;
            this.end = end;
        }

    });

    function registerCanvas() {
        var canvasTag = $('<canvas>');
        this.rootNode.html(canvasTag);
        this.canvas = new Tmt.Components.Canvas(canvasTag);
    }

    function drawWaves() {
        if (this.wave.length > 0) {
            // todo.
        }
    }

    function drawChart() {
        if (this.data.length > 0) {
            drawUI.call(this);
        }
    }

    function drawUI() {
        var height =  this.canvas.node.height,
            width =  this.canvas.node.width,
            context = this.canvas.context;

        context.beginPath();
        context.lineWidth = .5;
        context.strokeStyle = '#aaa';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.moveTo(10, height / 2);
        context.lineTo(width, height / 2);
        context.stroke();
        context.closePath();

        context.beginPath();
        context.lineWidth = .5;
        context.strokeStyle = '#aaa';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.moveTo(0,  0);
        context.lineTo(width, 0);
        context.stroke();
        context.closePath();

        context.beginPath();
        context.lineWidth = .5;
        context.strokeStyle = '#aaa';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.moveTo(0, height);
        context.lineTo(width, height);
        context.stroke();
        context.closePath();

         context.beginPath();
        context.fillStyle = '#aaa';
        context.textBaseline = 'middle';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.fillText("0", 0, height / 2);
        context.closePath();

        context.beginPath();
        context.fillStyle = '#aaa';
        context.textBaseline = 'top';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.fillText("+1", 0, 5);
        context.closePath();

        context.beginPath();
        context.fillStyle = '#aaa';
        context.textBaseline = 'bottom';
        context.shadowOffsetX = 0;
        context.shadowOffsetY = 0;
        context.shadowBlur = 0;
        context.fillText("-1", 0, height - 5);
        context.closePath();
    }

})(jQuery);

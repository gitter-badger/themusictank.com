(function ($, undefined) {

    "use strict";

    var UiRenderer = namespace("Tmt.Components.Chart").UiRenderer = function (start, end) {
        this.start = start;
        this.end = end;

    };

    inherit([Tmt.Components.Renderer], UiRenderer, {

        render: function () {
            var height = this.height(),
                width = this.width(),
                context = this.context();

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
            context.moveTo(0, 0);
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
    });

})(jQuery);

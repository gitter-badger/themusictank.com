export default class LineChartDataRenderer {

    constructor(data, start, end) {
        this.datasource = data;
        this.start = start ? start : 0;
        this.end = end ? end : data.length;
    }

    setCanvas(canvas) {
        this.padding = 10;
        this.labelWidth = 10;

        this.dotSize = 4;
        this.halfDot = (this.dotSize / 2);

        this.height = canvas.height;
        this.paddedHeight = this.height - (2 * this.padding);
        this.verticalMiddle = (this.paddedHeight / 2) + this.padding;

        this.width = canvas.width;
        this.paddedWidth = this.width - (2 * this.padding) - this.labelWidth;
        this.distanceBetweenPoints = (this.paddedWidth - this.halfDot) / (this.end - this.start);

        this.context = canvas.getContext('2d');
    }

    render() {
        drawRange.call(this);
        drawJoins.call(this);
        drawDots.call(this);
    }

}

function drawRange() {

        let i = this.start,
            len = this.end,
            yPos = this.padding,
            currentXPoint = this.labelWidth + this.padding + this.halfDot;

        if(this.datasource[i] && this.datasource[i].high_avg_groove)
        {
            yPos = (this.paddedHeight * (1-this.datasource[i].high_avg_groove)) + this.padding;
        }

        this.context.beginPath();
        this.context.fillStyle = "rgba(124,179,225,.5)";
        this.context.moveTo(currentXPoint - this.distanceBetweenPoints - this.halfDot, yPos);

        for( ; i < len; i++, yPos = 0)
        {
            if(this.datasource[i] && this.datasource[i].high_avg_groove)
            {
                yPos = (this.paddedHeight * (1-this.datasource[i].high_avg_groove)) + this.padding;
            }

            if(yPos)
            {
                this.context.lineTo(currentXPoint - this.halfDot, yPos);
            }

            currentXPoint += this.distanceBetweenPoints;
        }

        for( i = len ; i > this.start; i--, yPos = 0)
        {
            if(this.datasource[i] && this.datasource[i].low_avg_groove)
            {
                yPos = (this.paddedHeight * (1-this.datasource[i].low_avg_groove)) + this.padding;
            }

            if(yPos)
            {
                this.context.lineTo(currentXPoint - this.halfDot, yPos);
            }

            currentXPoint -= this.distanceBetweenPoints;
        }

        this.context.fill();
        this.context.closePath();
}

function drawJoins() {
    let i = this.start,
        len = this.end,
        currentXPoint = this.labelWidth + this.padding + this.halfDot,
        yPos, prevYPos = 0;

    for( ; i < len; i++, yPos = 0){

        if(this.datasource[i]) {
            yPos = (this.paddedHeight * (1-this.datasource[i].avg_groove)) + this.padding;
        }

        if(prevYPos > 0 && yPos > 0) {
            this.context.beginPath();
            this.context.lineWidth = 1;
            this.context.strokeStyle = "#4682b4";
            this.context.fillStyle = "#4682b4";
            this.context.shadowColor = "rgba( 0, 0, 0, 0.3 )";
            this.context.shadowOffsetX = 1;
            this.context.shadowOffsetY = 1;
            this.context.shadowBlur = 3;
            this.context.moveTo(currentXPoint - this.distanceBetweenPoints - this.halfDot, prevYPos);
            this.context.lineTo(currentXPoint - this.halfDot, yPos);
            this.context.stroke();
            this.context.closePath();
        }

        prevYPos = yPos;
        currentXPoint += this.distanceBetweenPoints;
    }

}

function drawDots() {

    let i = this.start,
        len = this.end,
        currentXPoint = this.labelWidth + this.padding + this.halfDot,
        twopi = 2 * Math.PI,
        frame,
        yPos;

    for( ; i < len; i++) {
        frame = this.datasource[i];
        if(frame) {
            yPos = (this.paddedHeight * (1-frame.avg_groove)) + this.padding;

            this.context.beginPath();
            this.context.fillStyle = "#4682b4";
            this.context.lineWidth = .5;
            this.context.strokeStyle = "#235680";
            this.context.shadowColor = "rgba( 0, 0, 0, 0.3 )";
            this.context.shadowOffsetX = 1;
            this.context.shadowOffsetY = 1;
            this.context.shadowBlur = 3;
            this.context.arc(currentXPoint - this.halfDot, yPos, this.dotSize, 0, twopi, false);
            this.context.fill();
            this.context.stroke();
            this.context.closePath();
        }
        currentXPoint += this.distanceBetweenPoints;
    }
}

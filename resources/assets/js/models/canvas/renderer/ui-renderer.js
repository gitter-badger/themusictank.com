export default class UiRenderer {

    constructor() {
        this.height = 0;
        this.width = 0;
        this.context = null;
    }

    setCanvas(canvas) {
        this.padding = 10;
        this.labelWidth = 10;

        this.height = canvas.height;
        this.paddedHeight = this.height - (2 * this.padding);
        this.verticalMiddle = (this.paddedHeight / 2) + this.padding;

        this.width = canvas.width;
        this.paddedWidth = this.width - (2 * this.padding) - this.labelWidth;

        this.context = canvas.getContext('2d');
    }

    render() {
        // middle groove line
        this.context.beginPath();
        this.context.lineWidth = .5;
        this.context.strokeStyle = '#aaa';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.moveTo(this.labelWidth + this.padding, this.verticalMiddle);
        this.context.lineTo(this.paddedWidth, this.verticalMiddle);
        this.context.stroke();
        this.context.closePath();

        // middle text
        this.context.beginPath();
        this.context.fillStyle = '#aaa';
        this.context.textBaseline = 'middle';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.fillText("0", this.padding, this.verticalMiddle);
        this.context.closePath();

        // top line
        this.context.beginPath();
        this.context.lineWidth = .5;
        this.context.strokeStyle = '#aaa';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.moveTo(this.labelWidth + this.padding, this.padding);
        this.context.lineTo(this.paddedWidth, this.padding);
        this.context.stroke();
        this.context.closePath();

        // top line text
        this.context.beginPath();
        this.context.fillStyle = '#aaa';
        this.context.textBaseline = 'middle';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.fillText("+1", this.padding, this.padding);
        this.context.closePath();

        // bottom
        this.context.beginPath();
        this.context.lineWidth = .5;
        this.context.strokeStyle = '#aaa';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.moveTo(this.labelWidth + this.padding, this.paddedHeight + this.padding);
        this.context.lineTo(this.paddedWidth, this.paddedHeight + this.padding);
        this.context.stroke();
        this.context.closePath();

        // this bottom line
        this.context.beginPath();
        this.context.fillStyle = '#aaa';
        this.context.textBaseline = 'middle';
        this.context.shadowOffsetX = 0;
        this.context.shadowOffsetY = 0;
        this.context.shadowBlur = 0;
        this.context.fillText("-1", this.padding, this.paddedHeight + this.padding);
        this.context.closePath();
    }
}

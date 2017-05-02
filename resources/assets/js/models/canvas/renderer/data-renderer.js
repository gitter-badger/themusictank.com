export default class DataRenderer {

    constructor(data) {
        this.data = data;
    }

    setCanvas(canvas) {
        this.height = canvas.height;
        this.width = canvas.width;
        this.context = canvas.getContext('2d');
    }

    render() {
        drawRange.call(this);
        drawJoins.call(this);
        drawDots.call(this);
    }

}

function drawRange() {

}

function drawJoins() {
    // var height = this.height(),
    //     width = this.width(),
    //     context = this.context();

    //     console.log(data);

}

function drawDots() {

}

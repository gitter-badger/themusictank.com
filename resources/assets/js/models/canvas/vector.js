export default class Vector {

    constructor(x, y) {
        this.y = y;
        this.x = x;
    }

    add(vector) {
        this.x = this.x + vector.x;
        this.y = this.y + vector.y;
    }

}

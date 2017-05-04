<script>
import ComponentBase from './mixins/base.js'
import {mapGetters} from 'vuex';
import Canvas from '../models/canvas/canvas.js'
import LineChartDataRenderer from '../models/canvas/renderer/line-chart-data-renderer.js'
import UiRenderer from '../models/canvas/renderer/ui-renderer.js'


export default {
    mixins: [ComponentBase],

    props: ['objectId', 'start', 'end', 'datasource'],

    computed: {
        ...mapGetters(['reviewFrames']),

        height() {
            return this.getElement().height();
        },

        width() {
            return this.getElement().width();
        },

        dataset() {
            if (this.reviewFrames) {
                return this.reviewFrames.getTrackData(this.objectId, this.datasource);
            }
        }
    },

    data () {
        return {
            'canvas' : null
        }
    },

    watch : {
        dataset() {
            this.canvas.addRenderer(new UiRenderer());
            this.canvas.addRenderer(new LineChartDataRenderer(this.dataset, this.start, this.end));
            this.canvas.draw();
        }
    },

    mounted() {
        let element = this.getElement();

        this.canvas = new Canvas(this.getElement().find('canvas'));
        this.canvas.resize(element.width(), element.height());

        $(window).on('resize', this.debounce(() => {
            this.canvas.resize(element.width(), element.height());
            console.log('resize with ' + element.width() + " , " + element.height());
            this.canvas.draw();
        }));

    }
};
</script>


<template>
    <div class="ctrl ctrl-line-chart">
        <canvas></canvas>
    </div>
</template>


<style lang="scss">
.ctrl-upvote {
    background: #efefef;
    border: 1px solid #dedede;
    border-radius: 3px;
    display: inline-block;
    text-align: center;
    margin:1px 0;
    width: 66px;
    vertical-align: middle;

    &.liked {
        li:nth-child(2) {
            display:none;
        }
    }

    &.disliked {
        li:nth-child(1) {
            display:none;
        }
    }

    ul {
        display: block;
        list-style-type: none;
        padding: 0;
        margin: auto;

        li {
            display: inline-block;
            padding: 0;
            margin: 0;
        }
    }

    button {
        border: none;
        background: #efefef;
        cursor: pointer;
        color:#333;
        font-size: 14px;
        text-shadow: 0 1px #fff;
        height: 30px;
        width: 30px;

        &:hover {
            color:blue;
        }

        &:disabled, &:disabled:hover {
            color:#ccc;
        }
    }
}
</style>

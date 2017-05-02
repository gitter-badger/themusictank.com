<script>
import ComponentBase from './mixins/base.js'
import {mapGetters} from 'vuex';
import Canvas from '../models/canvas/canvas.js'
import DataRenderer from '../models/canvas/renderer/data-renderer.js'
import UiRenderer from '../models/canvas/renderer/ui-renderer.js'


export default {
    mixins: [ComponentBase],

    props: ['objectId', 'start', 'end'],

    computed: {
        ...mapGetters(['reviewFrames']),
        value() {
            if (this.upvotes) {
                return this.upvotes.getVote(this.type, this.objectId);
            }
            return -1;
        },

        height() {
            return this.getElement().height();
        },

        width() {
            return this.getElement().width();
        }
    },

    data () {
        return {
            'canvas' : null
        }
    },

    mounted() {
        let element = this.getElement();

        this.canvas = new Canvas(this.getElement().find('canvas'));
        this.canvas.resize(element.width(), element.height());

        // $(window).on('resize', this.debounce(() => {
        //     this.canvas.resize(element.width(), element.height());
        //     this.canvas.draw();
        // }));

        this.canvas.addRenderer(new UiRenderer());
        this.canvas.addRenderer(new DataRenderer([]));
        this.canvas.draw();
    },

    methods: {

        is(value) {
            return this.value == value;
        },

        save(value) {
            let action = this.type == "track" ? "addTrackUpvote" : "addAlbumUpvote";
            let payload = { id: this.objectId, vote: value };

            this.lock();
            this.ajax()
                .post('/ajax/' + action, payload)
                .then((response) => {
                    this.$store.commit(action, payload);
                    this.unlock();
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.unlock();
                });
        },

        remove(value) {
            let action = this.type == "track" ? "removeTrackUpvote" : "removeAlbumUpvote";
            let payload = { id : this.objectId };

            this.lock();
            this.ajax()
                .post('/ajax/' + action, payload)
                .then((response) => {
                    this.$store.commit(action, this.objectId);
                    this.unlock();
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.unlock();
                });
        },

        like() {
            this.is(types.like) ? this.remove() : this.save(types.like);
        },

        dislike() {
            this.is(types.dislike) ? this.remove() : this.save(types.dislike);
        },

        lock () {
            this.enabled = false;
        },

        unlock () {
            this.enabled = true;
        }
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

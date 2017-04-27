<script>
import ComponentBase from '../mixins/base.js'
import Bar from './Bar.vue'
import CursorCtl from './CursorCtl.vue'

export default {
    components: {
        Bar, CursorCtl
    },
    mixins: [ComponentBase],
    props: ['totalPositionUnits', 'totalBufferedUnits', 'currentPositionProgress', 'currentBufferedProgress'],
    methods: {
        onSeek(evt) {
            let progressBar = this.getElement().find('.progress'),
                offset = progressBar.offset(),
                relX = evt.pageX - offset.left,
                pctLocation = relX / progressBar.width();

            this.$emit("seek", pctLocation * this.totalPositionUnits);
        }
    }
};
</script>


<template>
    <div class="ctrl ctrl-progress">
        <div class="progress" @click="onSeek">
            <bar class="loaded-bar" :total-units="totalBufferedUnits" :position="currentBufferedProgress"></bar>
            <bar class="playing-bar" :total-units="totalPositionUnits" :position="currentPositionProgress"></bar>
        </div>
        <cursor-ctl :total-units="totalPositionUnits" :position="currentPositionProgress"></cursor-ctl>
    </div>
</template>


<style lang="scss">
    .ctrl-progress {
        position: absolute;
        top: 30px;
        left: 80px;
        right: 80px;
        cursor: pointer;

        .progress {
            height: 5px;
            background: #ccc;
        }

        .loaded-bar {
            background: orange;
            left: 0;
            position: absolute;
            height: 5px;
        }

        .playing-bar {
            left: 0;
            position: absolute;
            height: 5px;
            background: cyan;
        }
    }
</style>

<script>
import ComponentBase from '../mixins/base.js'
import Bar from './Bar.vue'

export default {
    components: {
        Bar
    },
    mixins: [ComponentBase],
    props: ['totalUnits', 'currentPositionProgress', 'currentLoadedProgress']
};
</script>


<template>
    <div class="ctrl ctrl-progress">
        <div class="progress">
            <bar class="loaded-bar" :total-units="totalUnits" :position="currentLoadedProgress"></bar>
            <bar class="playing-bar" :total-units="totalUnits" :position="currentPositionProgress"></bar>
        </div>
        <div class="cursor"></div>
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

        .cursor {
            background: #fff;
            box-shadow: 0 1px 3px #000;
            border-radius: 20px;
            width: 20px;
            height: 20px;
            position: absolute;
            top: 2px;
            margin: -10px 0 0 -10px;
        }
    }
</style>

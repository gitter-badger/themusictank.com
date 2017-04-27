<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: ['totalUnits', 'position'],
    computed: {
        cssLeft() {
            let pct = (this.position / this.totalUnits) * 100;
            return "left: " + pct + "%;";
        }
    }
};
</script>

<template>
    <div class="cursor" :style="cssLeft"></div>
</template>

<style lang="scss">
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
</style>

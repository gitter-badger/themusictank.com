<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    data() {
        return {
            'totalUnits': 0,
            'position': 0,
        }
    },

    methods: {
        getCSSWidth() {
            let pct = (this.position / this.totalLengthUnits) * 100;
            return "width: " + pct + "%;";
        }
    }
};
</script>

<template>
    <div class="progress-bar" role="progressbar" :aria-valuenow="position" aria-valuemin="0" :aria-valuemax="totalUnits" :style="getCSSWidth()">
    </div>
</template>

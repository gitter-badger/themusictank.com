<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: ['totalUnits', 'position'],
    computed: {
        cssWidth() {
            let pct = (this.position / this.totalUnits) * 100;
            return "width: " + pct + "%;";
        }
    }
};
</script>

<template>
    <div class="progress-bar" role="progressbar" :aria-valuenow="position" aria-valuemin="0" :aria-valuemax="totalUnits" :style="cssWidth">
    </div>
</template>

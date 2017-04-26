<script>
import ComponentBase from './mixins/base.js'

export default {
    mixins: [ComponentBase],

    props: ['identity', 'location', 'label'],

    data () {
        return {
            'enabled' : true
        }
    },

    methods: {
        report() {
            this.lock();
            console.log("@todo");
            this.unlock();
        },

        lock() {
            this.enabled = false;
        },

        unlock() {
            this.enabled = true;
        }
    }
};
</script>


<template>
    <button class="report-bug" @click="report" :disabled="!enabled">
        <i class="fa fa-bug"></i> {{ label }}
    </button>
</template>


<style lang="scss">
.report-bug {
    display:inline-block;
    margin: 2px;
}
</style>

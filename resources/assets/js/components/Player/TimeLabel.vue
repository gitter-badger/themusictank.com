<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: ['time'],
    computed: {
        humanTime : function toReadableTime() {



            let time = new Date(1000 * this.time),
                mins = ("0" + time.getMinutes()).slice(-2),
                secs = ("0" + time.getSeconds()).slice(-2);

            return mins + ":" + secs;
        }
    }
};
</script>

<template>
    <span class="time">{{ humanTime }}</span>
</template>

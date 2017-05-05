<script>
import ComponentBase from './mixins/base.js'

export default {
    mixins: [ComponentBase],

    props: ['target', 'label', 'visible'],

    computed : {
        targetElement() {
            return $(this.target);
        }
    },

    data () {
        return {
            'visibilityState' : this.visible
        }
    },

    methods: {
        toggle() {
            this.visibilityState = !this.visibilityState;
            this.syncElement();
        },
        syncElement() {
           this.visibilityState ? this.targetElement.show() : this.targetElement.hide()
        }
    },

    mounted() {
        this.syncElement();
    }
};
</script>


<template>
    <div class="ctrl ctrl-visible-toggler">
        <button @click.prevent="toggle">
            <i v-if="!visibilityState" class="fa fa-square-o" aria-hidden="true"></i>
            <i v-if="visibilityState" class="fa fa-check-square-o " aria-hidden="true"></i>
        </button>
        {{ label }}
    </div>
</template>

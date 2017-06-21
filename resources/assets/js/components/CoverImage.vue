<script>
import ComponentBase from './mixins/base.js'

export default {
    mixins: [ComponentBase],

    props: [
        'thumbnail-mobile',
        'thumbnail',
        'cover-mobile',
        'cover',
        'blur-mobile',
        'blur',
        'hex',
        'alt'
    ],

    data() {
        return {
            threshold : 5,
            scrollY: 0,
            wrapperHeight: 1,
            imageHeight: 1
        }
    },

    computed: {

        gradient() {
            return "linear-gradient(180deg, transparent, " + this.hex + ")";
        },

        // hasImage() {
        //     return this.url != "";
        // },

        scrolledDistance() {
            return this.scrollY / 5;
        },

        opacity() {
            if (this.shouldApply) {

                let maxScroll = this.wrapperHeight * .95,
                    minScroll = this.wrapperHeight * .2;

                if (this.scrollY < minScroll) {
                    return 1;
                }

                if (this.scrollY > maxScroll) {
                    return 0;
                }

                return (maxScroll - this.scrollY) / maxScroll;
            }

            return 1;
        },

        shouldApply() {
            let scrolled = this.scrolledDistance;
            return this.scrolledDistance > this.threshold && this.scrolledDistance < this.wrapperHeight;
        }

    },

    mounted() {
        this.$el.parentElement.className += " ctrl-cover-image-parent ";

        var img = new Image();
        img.src = this.$refs.clean.src;
        img.addEventListener('load', () => {
            this.wrapperHeight = this.$el.clientHeight;
            this.imageHeight = this.$refs.clean.clientHeight;
        });

        window.addEventListener('scroll', () => {
            this.scrollY = window.scrollY;
        });
    }
};
</script>

<template>
    <div class="ctrl ctrl-cover-image" v-bind:title="alt">
        <img v-bind:src="thumbnail" v-bind:alt="alt" ref="clean">
        <i class="blur"></i>
        <i class="cover" v-bind:style="{ opacity: opacity }"></i>
        <i class="mask"></i>
        <i class="gradient" v-bind:style="{ background: gradient }"></i>
    </div>
</template>

<style lang="scss">
.ctrl-cover-image-parent {
    position: relative;
}

.ctrl-cover-image {
    position: relative;
    min-height: 400px;
    max-height: 120vh;
    overflow: hidden;

    i {
        background-size: cover;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;

        &.gradient {
            top: inherit;
            height: 600px;
        }

        &.mask {
            background: transparent url(http://static.themusictank.com/assets/images/triangles.png) top left repeat;
        }
    }

    img {
        width: 100%;

        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -o-user-select: none;
        user-select: none;

        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
    }
}
</style>

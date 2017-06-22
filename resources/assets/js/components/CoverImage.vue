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

        bottomGradient() {
            return "linear-gradient(180deg, transparent, " + this.hex + ")";
        },

        leftGradient() {
            return "linear-gradient(270deg, transparent, " + this.hex + ")";
        },

        // hasImage() {
        //     return this.url != "";
        // },

        scrolledDistance() {
            return this.scrollY / 5;
        },

        opacity() {
            if (this.shouldApply) {

                let maxScroll = this.wrapperHeight * .7,
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
    <div class="ctrl ctrl-cover-image cover-image" v-bind:title="alt">
        <img v-bind:src="thumbnail" v-bind:alt="alt" ref="clean">
        <i class="blur"></i>
        <i class="cover" v-bind:style="{ opacity: opacity }"></i>
        <i class="mask"></i>
        <i class="bottom-gradient" v-bind:style="{ background: bottomGradient }"></i>
        <i class="left-gradient" v-bind:style="{ background: leftGradient }"></i>
    </div>
</template>

<style lang="scss">
.ctrl-cover-image {
    position: absolute;
    min-height: 600px;
    max-height: 120vh;
    max-width: 900px;
    width: 100%;
    overflow: hidden;
    right: 0;

    i {
        background-size: cover;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;

        &.bottom-gradient {
            top: inherit;
            height: 60%;
        }

        &.left-gradient {
            display: none;
        }

        @media (min-width: 600px) {
            &.left-gradient {
                display: inline;
                right: 20%;
            }
        }

        &.mask {
            background: transparent url(http://static.themusictank.com/assets/images/triangles.png) top left repeat;
        }
    }

    img {
        width: 100%;
    }
}
</style>

<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    prop: ['saving', 'nextSongSlug', 'nextSongTitle', 'profileSlug', 'songName', 'albumTitle'],
    computed: {

        reviewUrl() {
            return "/tankers/" + profileSlug + "/review/" + songSlug + "/";
        }

    },

    methods: {
        popupFacebookShare() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + this.reviewUrl, 'facebook-share-dialog', 'width=626,height=436');
        },

        popupTwitterShare() {
            window.open('https://twitter.com/intent/tweet?text=' + encodeURI('Check out my review of ' + this.songName) + '!&amp;url=' + this.reviewUrl + '&amp;via=themusictank', 'twitter-share-dialog', 'width=626,height=436');
        },
    }
};
</script>

<template>
    <div class="next-step-mask">
        <div class="next-step">
            <h3>Review complete!</h3>

            <div class="saving" v-if="saving">
                <p>Just a sec, we are still saving your review...</p>
            </div>

            <div class="next" v-if="hasNext">
                <p>Why stop now? review <i>{{ nextSongTitle }}</i> the next track on {{ albumTitle }}.</p>
            </div>

            <div class="done" v-if="!saving && !hasNext">
                <p>Thanks for sharing your opinion!</p>
            </div>

            <div class="sharing">
                <p>Share your review on social networks:</p>
                <button type="button" @click.prevent="twitterSharePopup"><i class="fa fa-facebook"></i></button>
                <button type="button" @click.prevent="facebookSharePopup"><i class="fa fa-twitter"></i></button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.next-step-mask {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, .3);
    top: 0;
}

.next-step {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    .saving,
    .next,
    .done {
        display: none;
    }

    &.review-still-saving .saving {
        display: block;
    }

    &.next-track .next {
        display: block;
    }

    &.nothing-else .done {
        display: block;
    }
}
</style>

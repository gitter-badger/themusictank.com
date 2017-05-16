<script>
import ComponentBase from '../mixins/base.js'

export default {
    mixins: [ComponentBase],
    props: [
        'userSlug',
        'songSlug',
        'songName'
    ],
    computed: {
        reviewUrl() {
            return "/tankers/" + this.userSlug + "/review/" + this.songSlug + "/";
        }
    },
    methods: {
        popupFacebookShare() {
            window.open('https://www.facebook.com/sharer/sharer.php?u=' + this.reviewUrl, 'facebook-share-dialog', 'width=626,height=436');
        },

        popupTwitterShare() {
            window.open('https://twitter.com/intent/tweet?text=' + encodeURI('Check out my review of ' + this.songName) + '!&amp;url=' + this.reviewUrl + '&amp;via=themusictank', 'twitter-share-dialog', 'width=626,height=436');
        }
    }
};
</script>

<template>
    <div class="sharing">
        <p>Share your review on social networks:</p>
        <button type="button" @click.prevent="popupFacebookShare"><i class="fa fa-facebook"></i></button>
        <button type="button" @click.prevent="popupTwitterShare"><i class="fa fa-twitter"></i></button>
    </div>
</template>

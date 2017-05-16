<script>
import ComponentBase from '../mixins/base.js'
import ShareButtons from './ShareButtons.vue'

export default {
    components: { ShareButtons },
    mixins: [ComponentBase],
    prop: ['saving', 'nextSongSlug', 'nextSongName', 'profileSlug', 'songName', 'albumName'],
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
                <p>Why stop now? review <i>{{ nextSongName }}</i> the next track on {{ albumName }}.</p>
            </div>

            <div class="done" v-if="!saving && !hasNext">
                <p>Thanks for sharing your opinion!</p>
            </div>

            <share-buttons :user-slug="userSlug" :song-name="songName" :song-slug="songSlug"></share-buttons>
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

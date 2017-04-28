<script>
import ComponentBase from '../mixins/base.js'
import Reviewer from '../../models/reviewer.js'
import FxCanvas from '../Canvas/FxCanvas.vue'
import Knob from './Knob.vue'

export default {
    components: {
         Knob, FxCanvas
    },
    mixins: [ComponentBase],
    props: ['songTitle', 'songSlug', 'songVideo', 'profileSlug', 'albumTitle'],

    data() {
        return {
            'completed': false,
            'saving': false,
            'nextSongSlug': null,
            'nextSongTitle': null
        }
    },

    mounted() {
        this.reviewer = new Reviewer();
    },

    methods: {
        onStateChanged(player) {
            if (player.isPlaying()) {
                // review
            }

            this.completed = player.isCompleted();
        }
    }
};
</script>

<template>
    <div class="ctrl ctrl-reviewer">
        <fx-canvas></fx-canvas>
        <knob
            v-if="!completed"
            value="reviewer.currentGroove"
        ></knob>
        <completed-dialogs
            v-if="completed"
            :profile-slug="profileSlug"
            :next-song-slug="nextSongSlug"
            :next-song-title="nextSongTitle"
            :song-title="songTitle"
            :album-title="albumTitle"
        ></completed-dialogs>
    </div>
</template>


<style lang="scss">
.ctrl-player {
    position:absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    height:auto;
    z-index: 2;
    background: #fff;

    .ui {
        position:absolute;
        bottom: 70px;
        left:0;
        right: 0;
        background:transparent;
        border:none;
        z-index: 2;
    }
}

.ctrl-reviewer {
    position:absolute;
    top: 70px;
    left: 0;
    right: 0;
    bottom: 0;
    color: #fff;
    z-index: 2;

    canvas {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1;
    }
}
</style>

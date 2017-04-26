import 'babel-polyfill'
import './bootstrap'

import store from './store'
import Vue from 'vue'
import {mapGetters} from 'vuex';

import SearchForm from './components/SearchForm.vue'
import Upvote from './components/Upvote.vue'

import Profile from './models/profile'
import ReviewFrames from './models/review-frames'
import Upvotes from './models/upvotes'

Tmt.app = new Vue({
    el: 'section.app',
    store,
    components : {
        SearchForm, Upvote
    },
    methods : {
        error (er) {
            console.log(er);
        },

        profile (data) {
            let profile = new Profile(data);
            this.$store.commit('updateProfile', profile);
        },

        upvotes (data) {
            let upvote = new Upvotes(data);
            this.$store.commit('updateVotes', upvote);
        },

        reviewFrames (key, data) {
            let frames = new ReviewFrames(data);
            this.$store.commit('updateFrames', { key, frames })
        },

        setWaveData (data) {
            // this.$store.commit('wave', { data })
        }
    },
    computed: mapGetters(['profile','reviewFrames'])
});

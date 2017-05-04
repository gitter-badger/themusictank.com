import 'babel-polyfill'
import './bootstrap'

import store from './store'
import Vue from 'vue'
import {mapGetters} from 'vuex';

import SearchForm from './components/SearchForm.vue'
import Upvote from './components/Upvote.vue'
import BugReport from './components/BugReport.vue'
import Player from './components/Player/Player.vue'
import LineChart from './components/LineChart.vue'
import Notifier from './components/Notifier.vue'

import Profile from './models/profile'
import ReviewFrameCache from './models/review-frames/cache'
import ActivitiesCache from './models/activities/cache'
import Upvotes from './models/upvotes'

Tmt.app = new Vue({
    el: 'section.app',
    store,
    components : {
        SearchForm, Upvote, BugReport, Player, LineChart, Notifier
    },
    methods : {
        error (er) {
            console.log(er);
        },

        profile (data) {
            this.$store.commit('updateProfile', new Profile(data));
        },

        upvotes (data) {
            this.$store.commit('updateVotes', new Upvotes(data));
        },

        reviewFrames (data) {
            let cache = new ReviewFrameCache();
            data.forEach((row) => { cache.addTrack(row); });
            this.$store.commit('updateFrames', cache);
        },

        activities (data) {
            let cache = new ActivitiesCache();
            data.forEach((row) => { cache.addActivity(row); });
            this.$store.commit('updateActivities', cache);
        },

        setWaveData (data) {
            // this.$store.commit('wave', { data })
        }
    },
    computed: mapGetters(['profile','reviewFrames'])
});

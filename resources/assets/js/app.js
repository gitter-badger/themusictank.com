import 'babel-polyfill'
import './bootstrap'

import store from './store'
import Vue from 'vue'
import {mapGetters} from 'vuex';

import SearchForm from './components/SearchForm.vue'
import Upvote from './components/Upvote.vue'
import BugReport from './components/BugReport.vue'
import Follow from './components/Follow.vue'
import Player from './components/Player/Player.vue'
import LineChart from './components/LineChart.vue'
import Notifier from './components/Notifier.vue'
import VisibleToggler from './components/VisibleToggler.vue'
import ShareButtons from './components/Reviewer/ShareButtons.vue'
import CoverImage from './components/CoverImage.vue'

import User from './models/user'
import Upvotes from './models/upvotes'
import ReviewFrameCache from './models/review-frames/cache'
import ActivitiesCache from './models/activities/cache'
import SubscriptionsCache from './models/subscriptions/cache'

Tmt.app = new Vue({
    el: 'section.app',
    store,
    components : {
        SearchForm, Upvote, BugReport, Player, LineChart, Notifier, VisibleToggler, ShareButtons, Follow, CoverImage
    },
    methods : {
        error (er) {
            console.log(er);
        },

        user (data) {
            this.$store.commit('updateUser', new User(data));
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

        subscriptions (data) {
            let cache = new SubscriptionsCache();
            data.forEach((row) => { cache.addUser(row); });
            this.$store.commit('updateSubscriptions', cache);
        },

        setWaveData (data) {
            // this.$store.commit('wave', { data })
        }
    },
    computed: mapGetters(['user','reviewFrames'])
});

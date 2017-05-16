import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        user: null,
        upvotes: null,
        frames: null,
        activities: null,
        subscriptions: null
    },

    getters: {
        user(state) {
            return state.user;
        },

        upvotes(state) {
            return state.upvotes;
        },

        reviewFrames(state) {
            return state.frames;
        },

        activities(state) {
            return state.activities;
        },

        subscriptions(state) {
            return state.subscriptions;
        }
    },

    mutations: {
        updateActivities(state, activities) {
            state.activities = activities;
        },

        updateUser(state, user) {
            state.user = user;
        },

        updateSubscriptions(state, subscriptions) {
            state.subscriptions = subscriptions;
        },

        updateFrames(state, framesCache) {
            state.frames = framesCache;
        },

        updateVotes(state, upvotesCache) {
            state.upvotes = upvotesCache;
        },

        addTrackUpvote(state, { track_id, vote }) {
            let upvotesCache = state.upvotes;
            upvotesCache.addTrackUpvote(track_id, vote);
            state.upvotes = upvotesCache;
        },

        removeTrackUpvote(state, track_id) {
            let upvotesCache = state.upvotes;
            upvotesCache.removeTrackUpvote(track_id);
            state.upvotes = upvotesCache;
        },

        addAlbumUpvote(state, { album_id, vote }) {
            let upvotesCache = state.upvotes;
            upvotesCache.addAlbumUpvote(album_id, vote);
            state.upvotes = upvotesCache;
        },

        removeAlbumUpvote(state, album_id) {
            let upvotesCache = state.upvotes;
            upvotesCache.removeAlbumUpvote(album_id);
            state.upvotes = upvotesCache;
        }
    }

});

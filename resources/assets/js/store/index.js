import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        profile: null,
        upvotes: null,
        frames: {}
    },

    getters: {
        profile(state) {
            return state.profile;
        },

        upvotes(state) {
            return state.upvotes;
        },

        reviewFrames(state) {
            return state.frames;
        }
    },

    mutations: {
        updateProfile(state, profile) {
            state.profile = profile;
        },

        updateFrames(state, framesCache) {
            state.frames = framesCache;
        },

        updateVotes(state, upvotesCache) {
            state.upvotes = upvotesCache;
        },

        addTrackUpvote(state, { id, vote }) {
            let upvotesCache = state.upvotes;
            upvotesCache.addTrackUpvote(id, vote);
            state.upvotes = upvotesCache;
        },

        removeTrackUpvote(state, id) {
            let upvotesCache = state.upvotes;
            upvotesCache.removeTrackUpvote(id);
            state.upvotes = upvotesCache;
        },

        addAlbumUpvote(state, { id, vote }) {
            let upvotesCache = state.upvotes;
            upvotesCache.addAlbumUpvote(id, vote);
            state.upvotes = upvotesCache;
        },

        removeAlbumUpvote(state, id) {
            let upvotesCache = state.upvotes;
            upvotesCache.removeAlbumUpvote(id);
            state.upvotes = upvotesCache;
        }
    }

});

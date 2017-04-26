import Vue from 'vue'
import MyComponent from '../../../resources/assets/js/components/Upvote.vue'

describe("Upvote", function () {

    it('has a created hook', () => {
        expect(typeof Upvote.created).toBe('function')
    })

});

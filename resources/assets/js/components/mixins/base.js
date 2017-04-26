import $ from 'jquery'
import store from '../../store'
import axios from 'axios'

export default {
    store,
    methods: {
        getElement () {
            return $(this.$el);
        },

        store () {
            return this.$store;
        },

        state () {
            return this.$store.state;
        },

        ajax () {
            return axios.create({
                headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                     'X-Requested-With' : 'XMLHttpRequest'
                }
            });
        }
    }
}

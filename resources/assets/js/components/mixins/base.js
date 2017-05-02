import $ from 'jquery'
import store from '../../store'
import axios from 'axios'

export default {
    store,
    methods: {
        getElement() {
            return $(this.$el);
        },

        store() {
            return this.$store;
        },

        state() {
            return this.$store.state;
        },

        ajax() {
            return axios.create({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        },

        debounce(func, threshold, execAsap) {
            var timeout;
            return function debounced() {
                var obj = this, args = arguments;
                function delayed() {
                    if (!execAsap)
                        func.apply(obj, args);
                    timeout = null;
                };

                if (timeout)
                    clearTimeout(timeout);
                else if (execAsap)
                    func.apply(obj, args);

                timeout = setTimeout(delayed, threshold || 100);
            };
        }
    }
}

<script>
import ComponentBase from './mixins/base.js'
import { mapGetters } from 'vuex';

export default {
    mixins: [ComponentBase],

    props: ['userId'],

    computed: {
        ...mapGetters(['subscriptions']),
        followed() {
            if (this.subscriptions) {
                return this.subscriptions.subscribed(this.userId);
            }
            return false;
        }
    },

    data() {
        return {
            'enabled': true
        }
    },

    methods: {
        follow() {
            let payload = { sub_id: this.userId };
            this.lock();
            this.ajax()
                .post('/ajax/tanker/follow/', payload)
                .then((response) => {
                    this.subscriptions.addUser(response.data);
                    this.$store.commit('updateSubscriptions', this.subscriptions);
                    this.unlock();
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.unlock();
                });
        },

        unfollow() {
            let payload = { sub_id: this.userId };
            this.lock();
            this.ajax()
                .post('/ajax/tanker/unfollow/', payload)
                .then((response) => {
                    this.subscriptions.removeUser(this.userId);
                    this.$store.commit('updateSubscriptions', this.subscriptions);
                    this.unlock();
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.unlock();
                });
        },

        lock() {
            this.enabled = false;
        },

        unlock() {
            this.enabled = true;
        }
    }
};
</script>


<template>
    <div class="ctrl ctrl-follow">
        <button class="followed" v-if="followed" @click.prevent="unfollow" :disabled="!enabled">
            Unsubscribe
        </button>
        <button class="unfollowed" v-if="!followed"  @click.prevent="follow" :disabled="!enabled">
            Subscribe
        </button>
    </div>
</template>


<style lang="scss">
.ctrl-upvote {
    background: #efefef;
    border: 1px solid #dedede;
    border-radius: 3px;
    display: inline-block;
    text-align: center;
    margin: 1px 0;
    width: 66px;
    vertical-align: middle;

    &.liked {
        li:nth-child(2) {
            display: none;
        }
    }

    &.disliked {
        li:nth-child(1) {
            display: none;
        }
    }

    ul {
        display: block;
        list-style-type: none;
        padding: 0;
        margin: auto;

        li {
            display: inline-block;
            padding: 0;
            margin: 0;
        }
    }

    button {
        border: none;
        background: #efefef;
        cursor: pointer;
        color: #333;
        font-size: 14px;
        text-shadow: 0 1px #fff;
        height: 30px;
        width: 30px;

        &:hover {
            color: blue;
        }

        &:disabled,
        &:disabled:hover {
            color: #ccc;
        }
    }
}
</style>

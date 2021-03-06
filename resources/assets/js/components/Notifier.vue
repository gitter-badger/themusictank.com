<script>
import ComponentBase from './mixins/base.js'
import { mapGetters } from 'vuex';

export default {
    mixins: [ComponentBase],

    props: ['href'],

    computed: {
        ...mapGetters(['activities']),
        notifications() {
            return this.activities ? this.activities.getAll() : [];
        },
        newNotifications() {
            // @todo : getNew needs to be exposed as a property to register into Vue
            return this.activities ? this.activities.getNew() : [];
        },
        newNotificationsCount() {
            return this.newNotifications.length;
        },
        count() {
            return this.activities ? this.activities.count() : 0;
        },
        hasNotifications() {
            return this.count > 0
        },
        hasNewNotifications() {
            return this.newNotificationsCount > 0
        }
    },

    data() {
        return {
            opened: false,
            enabled: true
        }
    },

    methods: {
        toggle() {
            this.opened = !this.opened;
        },

        lock() {
            this.enabled = false;
        },

        unlock() {
            this.enabled = true;
        },

        stfu() {
            let ids = this.newNotifications.map((row) => { return row.id; });
            this.clearNotifications(ids);
        },

        listItemClick(evt) {
            let item = evt.target;
            if (item.getAttribute('hide-click')) {
                this.clearNotifications([item.getAttribute('id')], item.getAttribute("href"));
            }
        },

        clearNotifications(notificationsIds, destinationUrl) {
            if (notificationsIds.length < 1) {
                return;
            }

            let payload = {
                'ids': notificationsIds
            };

            this.lock();
            this.ajax()
                .post('/ajax/tanker/ok-stfu', payload)
                .then((response) => {
                    this.unlock();

                    payload.ids.forEach((id) => {
                        let activity = this.activities.findById(id);
                        if (activity) {
                            activity.must_notify = false;
                        }
                    });

                    if (destinationUrl) {
                        window.location = destinationUrl;
                    }
                })
                .catch((error) => {
                    Tmt.app.error(error);
                    this.unlock();
                });
        }
    },

    mounted() {
        setTimeout(() => {
            this.ajax()
                .post('/ajax/tanker/whats-up', { "timestamp": Math.floor(Date.now() / 1000) })
                .then((response) => {
                    if (response.data.length) {
                        response.data.forEach((row) => { this.activities.addActivity(row); });
                        this.$store.commit('updateActivities', this.activities);
                    }
                })
                .catch((error) => {
                    Tmt.app.error(error);
                });
        }, 1000 * 60);
    }
};
</script>


<template>
    <div class="ctrl ctrl-notifier" :class="{ 'ring-a-ding' : hasNewNotifications }">
        <a :href="href" @click.prevent="toggle">
            <i class="fa fa-bell"></i>
            <em v-if="hasNewNotifications">{{ newNotificationsCount }}</em>
        </a>
        <div class="panel" v-if="opened">
            <button name="stfu" @click.prevent="stfu" :disabled="!enabled">Mark all as read</button>
            <ul>
                <li v-for="notification in notifications" :class="{ 'new': notification.must_notify, 'read': !notification.must_notify }">
                    <a v-if="notification.getLink()" @click.prevent="listItemClick" :href="notification.getLink()" :id="notification.id" :hide-click="notification.must_notify">
                        {{ notification.getLabel() }}
                    </a>
                    <span v-else>{{ notification.getLabel() }}</span>
                </li>
                <li v-if="!hasNotifications" class="no-notices">You have no notifications for the moment.</li>
                <li class="view-all">
                    <a :href="href">View all notifications</a>
                </li>
                <li class="close" @click.prevent="toggle">Close</li>
            </ul>

        </div>
    </div>
</template>


<style lang="scss">
.ctrl-notifier {
    a {
        color: #dedede;
        display: inline-block;
        position: relative;

        em {
            position: absolute;
            bottom: 0;
            right: -5px;
            background: #ff0000;
            color: #fff;
            font-weight: bold;
        }
    }

    &.ring-a-ding {
        a {
            color: blue;
        }
    }
}
</style>

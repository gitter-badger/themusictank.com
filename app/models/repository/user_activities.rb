module Repository
    module UserActivities
        include Repository::Base

        def has_unread_notifications? user
            unread_notifications(user).any?
        end

        def unread_notifications user
            where( :user_id => user.id, :must_notify_user => 1)
        end

        def find_notifications_for_user user
            where(:user_id => user.id, :must_notify_user => [0,1])
        end

        def find_paginated_notifications_for_user user, params
            find_notifications_for_user(user).paginate(:page => params[:page]).per_page(15)
        end

        def user_was_rewarded? user, reward
            UserActivity.where(:user_id => user.id, :linked_obj_type => reward.key).exists?
        end

        def mark_user_notifications_read user
            UserActivity.where(:user_id => user.id, :must_notify_user => 1).update_all(:must_notify_user => 0)
        end

    end
end

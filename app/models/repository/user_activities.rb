module Repository
    module UserActivities
        include Repository::Base

        def has_unread_notifications? user
            unread_notifications(user).any?
        end

        def unread_notifications user
            where( :user_id => user.id, :must_notify_user => 1)
        end

        def find_paginated_notifications_for_user user, params
            where(:user_id => user.id, :must_notify_user => [0,1]).paginate(:page => params[:page]).per_page(15)
        end

        def user_was_rewarded? user, reward
            UserActivity.where(:user_id => user.id, :linked_obj_type => reward.key).exists?
        end

    end
end

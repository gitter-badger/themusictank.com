module Entity
    # Represent a release by a musician or band.
    class User < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def reward_account_creation
            Services::TMTAchievement::CreatedAccount.new.reward!(self)
        end

        def has_unread_notifications?
            ::UserActivity.has_unread_notifications?(self)
        end

        def unread_notifications_count
            ::UserActivity.unread_notifications(self).count
        end

    end
end

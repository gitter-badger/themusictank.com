module Services
    module TMTAchievement
        class Base < Services::Base

            def slug
                self.title.parameterize
            end

            # Allow classes to specify if they increment values
            # of it it's a unique match
            def unique?
                true
            end

            # Specifies if there is custom validation to be executed
            # before saving the achievement
            def validates? user
                true
            end

            # Checks if the user has been rewarded the achievement
            def rewarded? user
                UserActivity.user_was_rewarded?(user, self)
            end

            # Rewards the current achievement to the user
            def reward! user
                if unique? && !rewarded?(user)
                    if validates?(user)
                        achievement = Achievement.create!(:user_id => user.id, :slug => self.slug)
                        UserActivity.create!(:user_id => user.id, :linked_obj_type => self.class.name, :linked_obj_id => achievement.id, :must_notify_user => true)
                    end
                end
            end

        end
    end
end

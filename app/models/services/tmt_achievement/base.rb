module Services
    module TMTAchievement
        class Base < Services::Base

            def self.factory_by_slug slug
                Services::TMTAchievement.const_get(slug.camelize).new
            end

            def slug
               fullpath = self.class.instance_method('title').source_location.first
               File.basename(fullpath, ".rb")
            end

            def title_context
                [self.title]
            end

            def meta_title
                self.title_context
            end

            def meta_keywords
                self.title_context + [ t("achievement") ]
            end

            def meta_description
                self.description
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

            def number_of_rewarded
                Achievement.count_rewardees slug
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

module Entity
    # Represent a release by a musician or band.
    class UserActivity < Entity::Base

        self.abstract_class = true

        def viewed?
            self.must_notify_user === 0
        end

        def type
            self.linked_obj_type
        end

        def linked_obj
            @linked_obj ||= self.linked_obj_type.constantize.new
        end

        def title
            linked_obj.title
        end

    end
end

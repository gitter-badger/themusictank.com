module Entity
    # Represent a release by a musician or band.
    class Notification < Entity::Base

        self.abstract_class = true

        def viewed?
            !self.is_viewed.nil? && self.is_viewed > 0
        end

    end
end

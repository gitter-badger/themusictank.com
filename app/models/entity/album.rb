module Entity
    # Represent a release by a musician or band.
    class Album < Entity::Sluggable

        self.abstract_class = true

        def getImageUrl format
            self.thumbnail_source
        end

    end
end

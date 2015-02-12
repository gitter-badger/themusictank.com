module Entity
    # Represent a release by a musician or band.
    class Track < Entity::Sluggable
        self.abstract_class = true
    end
end

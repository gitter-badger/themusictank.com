module Entity
    # Represent a release by a musician or band.
    class User < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

    end
end

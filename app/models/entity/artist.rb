module Entity

    # Represent a release by a musician or band.
    class Artist < Entity::Slugged
        include Entity::Thumbnailed
        include Entity::Oembedable

        self.abstract_class = true

    end
end

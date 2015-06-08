module Entity
    # Represent a release by a musician or band.
    class Album < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def previous? track
            ::Album.has_previous? self, track
        end

        def previous track
            ::Album.find_previous self, track
        end

        def next? track
            ::Album.has_next? self, track
        end

        def next track
            ::Album.find_next self, track
        end

    end
end

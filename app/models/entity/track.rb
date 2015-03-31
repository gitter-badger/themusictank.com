module Entity
    # Represent a release by a musician or band.
    class Track < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def player_attributes
            return { "class"=> "streamer", "data-song-vid" => self.youtube_key } unless self.youtube_key.nil?
            return { "class"=> "streamer", "data-song" => self.slug }
        end

        def previous?
            self.position > 1
        end

        def previous
            Album.find_previous self
        end

        def next?
            Album.has_next? self
        end

        def next
            Album.find_next self
        end

    end
end

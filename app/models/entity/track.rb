module Entity
    # Represent a release by a musician or band.
    class Track < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def player_attributes
            return { "class"=> "streamer", "data-song-vid" => self.youtube_key } unless self.youtube_key.nil?
            return { "class"=> "streamer", "data-song" => self.slug }
        end
    end
end

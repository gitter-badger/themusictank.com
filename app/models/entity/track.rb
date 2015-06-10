module Entity
    # Represent a release by a musician or band.
    class Track < Entity::Slugged
        include Entity::Thumbnailed
        include Entity::Oembedable

        self.abstract_class = true

        def artist
            albums.first.artist
        end

        def title_context
            title = Array.new
            title << self.title

            self.albums.each do |album|
                title << album.title
            end

            title << self.artist.name
            title
        end

        def meta_title
            self.title_context
        end

        def meta_keywords
            self.title_context + [ t("track review") ]
        end

        def meta_description
            str = "View the reviewing statistics of %s, a song by %s."
            sprintf(str, self.title, self.artist.name)
        end

        def player_attributes
            return { "class"=> "streamer", "data-song-vid" => self.youtube_key } unless self.youtube_key.nil?
            return { "class"=> "streamer", "data-song" => self.slug }
        end

        def previous?
            self.position > 1
        end

        def previous
            ::Album.find_previous(self).first
        end

        def previous?
            ::Album.has_previous? self
        end

        def next?
            ::Album.has_next? self
        end

        def next
            ::Album.find_next(self).first
        end

    end
end

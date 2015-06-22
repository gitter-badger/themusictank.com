module Repository
    # This class handles queries of Albums objects.
    module Albums
        include Repository::Behavior::Thumbnailed
        include Repository::Behavior::Slugged
        include Repository::Behavior::Lastfmd

        # Lists artists that have no discography attached.
        def find_with_no_tracks
            Album.where(['id not in (?)', find_with_tracks.select("id") ])
        end

        # Lists artists that have discographies.
        def find_with_tracks
            Album.joins(:tracks).group("albums.id")
        end

        # def search criteria, limit = 10
        #     sanitized_position = sprintf("length(ltrim(title, %s)) as match_position", sanitize(criteria))
        #     select("*, #{sanitized_position}")
        #         .where('title LIKE ?', "%#{criteria}%")
        #         .order('match_position ASC')
        #         .limit(limit)
        # end

        def search criteria, limit = 10
            # http://stackoverflow.com/questions/22435780/how-to-order-results-by-closest-match-to-query
            regexp = /#{criteria}/i;
            result = order(:name).where("name ILIKE ?", "%#{criteria}%").limit(limit)
            result.sort{|x, y| (x =~ regexp) <=> (y =~ regexp) }
        end


        def find_all_previous album, track
            select("tracks.*")
                .joins(:tracks)
                .where('tracks.position < (?)', track.position)
                .where('albums.id = (?)', album.id)
                .order('tracks.position ASC')
        end

        def find_all_next album, track
            select("tracks.*")
                .joins(:tracks)
                .where('tracks.position > (?)', track.position)
                .where('albums.id = (?)', album.id)
                .order('tracks.position ASC')
        end

        def find_next album, track
            find_all_next(album, track).first
        end

        def find_previous album, track
            find_all_previous(album, track).first
        end

        def has_previous? album, track
            find_all_previous(album, track).any?
        end

        def has_next? album, track
            find_all_next(album, track).any?
        end
    end
end

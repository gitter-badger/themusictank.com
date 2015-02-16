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

        def search criteria, limit = 10
            sanitized_position = sprintf("length(ltrim(title, %s)) as match_position", sanitize(criteria))
            select("*, #{sanitized_position}")
                .where('title LIKE ?', "%#{criteria}%")
                .order('match_position ASC')
                .limit(limit)
        end
    end
end

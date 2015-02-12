module Repository
    # This class handles queries of Albums objects.
    module AlbumsTracks
        include Repository::Sluggables

        # Lists artists that have no discography attached.
        def find_with_no_tracks
            Album.where(['id not in (?)', find_with_tracks.select("id") ])
        end

        # Lists artists that have discographies.
        def find_with_tracks
            Album.joins(:tracks).group("albums.id")
        end

    end
end

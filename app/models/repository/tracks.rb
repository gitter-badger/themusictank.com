module Repository
    # This class handles queries of Track objects.
    module Tracks
        include Repository::Behavior::Slugged
        include Repository::Behavior::Youtubed
        include Repository::Behavior::Searchable

        def find_with_no_soundwave
            Track.where(['id not in (?)', find_with_soundwave.select("id") ])
        end

        # Lists artists that have discographies.
        def find_with_soundwave
            Track.joins(:track_soundwave)
        end

    end
end

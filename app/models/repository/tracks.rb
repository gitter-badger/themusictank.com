module Repository
    # This class handles queries of Track objects.
    module Tracks
        include Repository::Behavior::Slugged
        include Repository::Behavior::Youtubed
        include Repository::Behavior::Searchable

    end
end

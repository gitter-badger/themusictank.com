module Repository
    # This class handles queries of Track objects.
    module Tracks
        include Repository::Behavior::Slugged
        include Repository::Behavior::Youtubed

        def search criteria, limit = 10
            # http://stackoverflow.com/questions/22435780/how-to-order-results-by-closest-match-to-query
            regexp = /#{criteria}/i;
            result = order(:title).where("title ILIKE ?", "%#{criteria}%").limit(limit)
            result.sort{|x, y| (x =~ regexp) <=> (y =~ regexp) }
        end
    end
end

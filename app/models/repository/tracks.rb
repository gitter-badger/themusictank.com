module Repository
    # This class handles queries of Track objects.
    module Tracks
        include Repository::Behavior::Slugged


        def search criteria, limit = 10
            sanitized_position = sprintf("length(ltrim(title, %s)) as match_position", sanitize(criteria))
            select("*, #{sanitized_position}")
                .where('title LIKE ?', "%#{criteria}%")
                .order('match_position ASC')
                .limit(limit)
        end
    end
end

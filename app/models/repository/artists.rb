module Repository
    # This class handles queries of Artists objects.
    module Artists
        include Repository::Behavior::Thumbnailed
        include Repository::Behavior::Slugged
        include Repository::Behavior::Lastfmd
        include Repository::Behavior::Musicbrainzed

        # Updates a set of artists and marks them as current being popular.
        def update_popular artists
            Artist.update_all(["is_popular = id in (?)", artists.map(&:id)])
        end

        # Returns a list of popular artists
        def find_popular
            Artist.where(:is_popular => 1)
        end

        # Finds a list of the qty first popular artist by random order.
        def find_random_popular qty = 20

            random_ids = [-1]
            random_set = Array.new
            popular = find_popular
            popular_count = popular.count

            if popular_count > 0
                log "Starting loop for random ids."
                while random_ids.length < qty or random_ids.length >= popular_count
                    how_many_left = popular_count - random_ids.length
                    random_offset = rand(how_many_left)
                    match = popular.where('id NOT IN (?)', random_ids).offset(random_offset).first
                    unless match.nil?
                        random_ids << match.id
                        random_set << match
                    end
                end
            end

            random_set
        end

        def search criteria, limit = 10
            sanitized_position = sprintf("length(ltrim(name, %s)) as match_position", sanitize(criteria))
            select("*, #{sanitized_position}")
                .where('name LIKE ?', "%#{criteria}%")
                .order('match_position ASC')
                .limit(limit)
        end

    end
end

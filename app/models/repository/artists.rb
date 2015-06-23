module Repository
    # This class handles queries of Artists objects.
    module Artists
        include Repository::Behavior::Thumbnailed
        include Repository::Behavior::Slugged
        include Repository::Behavior::Lastfmd
        include Repository::Behavior::Musicbrainzed
        include Repository::Behavior::Searchable

        # Updates a set of artists and marks them as current being popular.
        def update_popular artists
            make_all_unpopular
            Artist.where(:id => artists.map(&:id)).update_all(:is_popular => 1)
        end

        def make_all_unpopular
            Artist.update_all(:is_popular => 0)
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

        def search_where_field
            "name"
        end

        def search_order_field
            :name
        end
    end
end

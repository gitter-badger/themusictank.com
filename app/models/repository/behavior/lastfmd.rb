module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Lastfmd
            include Repository::Base

            # Lists artists than haven't been updated in a while on Musicbrainz
            def find_expired_lastfm
                where('last_lastfm_update < ? or last_lastfm_update is null', lastfm_expiration_date)
                .order(created_at: :asc)
            end

            # Lastfm only needs to update once a month.
            def lastfm_expiration_date
                Date.today - 30
            end
        end
    end
end

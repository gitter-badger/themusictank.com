module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Lastfmd
            include Repository::Base

            # Lists artists than haven't been updated in a while on Musicbrainz
            def find_expired_lastfm
                where 'last_lastfm_update < ? or last_lastfm_update is null', expiration_date
            end

            # Lastfm only needs to update once a month.
            def expiration_date
                Date.today - 30
            end
        end
    end
end

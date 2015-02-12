module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Lastfmd
            include Repository::Base

            # Lists artists than haven't been updated in a while on Musicbrainz
            def find_expired_lastfm
                where('last_lastfm_update < ?', Date.today - 7)
            end
        end
    end
end

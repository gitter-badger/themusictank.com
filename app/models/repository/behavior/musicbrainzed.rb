module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Musicbrainzed
            include Repository::Base

            # Lists artists than haven't been updated in a while on Musicbrainz
            def find_expired_musicbrainz
                where('last_mb_update < ?', Date.today - 7)
            end
        end
    end
end

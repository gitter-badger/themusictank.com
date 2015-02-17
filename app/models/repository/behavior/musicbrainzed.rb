module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Musicbrainzed
            include Repository::Base

            # Lists artists than haven't been updated in a while on Musicbrainz
            def find_expired_musicbrainz
                where 'last_mb_update < ? or last_mb_update is null', musicbrainz_expiration_date
            end

            # Update MB every week in other to catch new releases
            def musicbrainz_expiration_date
                Date.today - 7
            end
        end
    end
end

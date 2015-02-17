module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Youtubed
            include Repository::Base

            # Lists artists than haven't been updated in a while on youtube
            def find_expired_youtube
                where 'last_youtube_update < ? or last_youtube_update is null', youtube_expiration_date
            end

            # Lastfm only needs to update once a month.
            def youtube_expiration_date
                Date.today - 30
            end

            def youtube_key_is_expired? entity
                entity.last_youtube_update.nil? or entity.last_youtube_update < youtube_expiration_date
            end
        end
    end
end

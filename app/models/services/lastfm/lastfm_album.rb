module Services
    module Lastfm
    # This class talks to the Lastfm API and generates
        # album entities from the API results
        class LastfmAlbum < Services::Lastfm::Base

            # Updates expired albums on TMT.
            def self.update_expired
                expired = Album.find_expired_lastfm
                expired_count = expired.count

                log "Found #{expired_count} expired albums."
                if expired_count > 0
                    expired.each do |album|
                        log "Updating #{album.title}"
                        # Update the album profile
                        update_album_profile album
                    end
                end
            end

            # Updates a TMT album profile from the information on LastFM
            def self.update_album_profile album
                remote = find_remote album
                unless remote.nil?
                    album.thumbnail_source = remote["image"][ remote["image"].length - 1 ]['#text']
                    album.playcount = remote['playcount']
                    album.last_lastfm_update = DateTime.now
                    # Save the updated entity
                    album.save
                end
            end

            # Finds or creates Artist entities from standard LastFM API return data.
            # Only saves albums when a musicbrainz id is passed through.
            def self.find_or_create lfm_album
                unless lfm_album["mbid"].empty?
                    Artist.where(mbid: lfm_album["mbid"]).first_or_create! do |album|
                        album.name = lfm_album["name"]
                        album.last_lastfm_update = 1.month.ago
                    end
                end
            end

            protected

            # Fetches album details on LastFM based on a name.
            def self.find_remote album
                log "Sending API request to 'get_info(:artist => #{album.artist.name}, :album => #{album.title})'"
                LastFM::Album.get_info(:artist => album.artist.name, :album => album.title)["album"]
            end

        end
    end
end

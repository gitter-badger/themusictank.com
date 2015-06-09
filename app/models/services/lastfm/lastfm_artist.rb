module Services
    module Lastfm
        # This class talks to the Lastfm API and generates
        # artist entities from the API results
        class LastfmArtist < Services::Lastfm::Base

            # Update the list of popular artists on TMT.
            def self.update_tmt_top_artists
                artists = remote_top_artists
                Artist.update_popular artists
                artists
            end

            # Updates expired artists on TMT.
            def self.update_expired
                expired = Artist.find_expired_lastfm.limit(300) # we aint got all day son
                expired_count = expired.count

                log "Found #{expired_count} expired artists."
                if expired_count > 0
                    expired.each do |artist|
                        log "Updating #{artist.name}"
                        update_artist_profile artist
                    end
                end
            end

            # Updates a TMT artist profile from the information on LastFM
            def self.update_artist_profile artist
                artist = append_remote_data artist, find_remote_artist(artist)
                artist.last_lastfm_update = DateTime.now
                artist.save
            end

            # Finds or creates Artist entities from standard LastFM API return data.
            # Only saves artists when a musicbrainz id is passed through.
            def self.find_or_create lfm_artist
                unless lfm_artist["mbid"].empty?
                    Artist.where(mbid: lfm_artist["mbid"]).first_or_create! do |artist|
                        artist.name = lfm_artist["name"]
                        artist.last_lastfm_update = 1.month.ago
                    end
                end
            end

            protected

            # Fetches the list of current top artists on
            # LastFm.
            def self.remote_top_artists
                log "Sending API request to 'get_top_artists()'"

                # [Achievement unlocked : Cultural Victory]
                # Default to united states as the country of the top artists because
                # no one cares what's hot elsewhere it would seem.
                generate_top_artists_datalist LastFM::Chart.get_top_artists :country => "United States"
            end

            # Fetches artist details on LastFM based on a name.
            def self.find_remote_artist artist
                log "Sending API request to 'get_info(:artist=> #{artist.name}, :mbid => #{artist.mbid})'"
                LastFM::Artist.get_info(:artist=> artist.name, :mbid => artist.mbid)["artist"]
            end

            # Formats a resultset of API data into a known data structure
            def self.generate_top_artists_datalist data
                list = Array.new
                data["artists"]["artist"].each do |artistData|
                    artist = find_or_create(artistData)
                    list << find_or_create(artist) unless artist.nil?
                end
                list
            end

            def self.prepare_similar_artists_by_names names
                similar_artists = Array.new
                similar_artist_ids = Array.new

                if !names.nil? && names.length > 0
                    log "Starting similar artists loop."
                    names.each do |similar|
                        similar_artist = Services::Musicbrainz::MusicbrainzArtist.find_or_create_by_name similar['name']
                        unless similar_artist.nil? or similar_artist.id.nil? or similar_artist_ids.include? similar_artist.id
                            similar_artist_ids << similar_artist.id
                            similar_artists << similar_artist
                        end
                    end
                else
                    log "No similar artists given."
                end
                similar_artists
            end

            def self.append_remote_data artist, remote
                artist.thumbnail_source = remote["image"][ remote["image"].length - 1 ]['#text']
                artist.bio = remote["bio"]["summary"]

                formatted_artists = format_similar_arists remote['similar']['artist']
                artist.similar_artists = prepare_similar_artists_by_names formatted_artists

                artist
            end

            def self.format_similar_arists remote_data

                similar_artists = remote_data

                # Lastfm API sends only one uncontained result
                # when there aren't multiple similar artists.
                if !remote_data.nil? && !remote_data.is_a?(Array) && remote_data.has_key?("name")
                    similar_artists = Array.new
                    similar_artists << remote_data
                end

                similar_artists
            end

        end
    end
end

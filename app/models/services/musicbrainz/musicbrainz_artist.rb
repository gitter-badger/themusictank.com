module Services
    module Musicbrainz
    # This class talks to the Musicbrainz API and generates
        # artist entities from the API results
        class MusicbrainzArtist < Services::Musicbrainz::Base

            # Populates the artists with empty discographies by querying MusicBrainz' database.
            def self.update_expired
                expired = Artist.find_expired_musicbrainz
                expired_count = expired.count
                log "Found #{expired_count} expired artists."
                if expired_count > 0
                    expired.each do |artist|
                        log "Updating #{artist.name}'s discography"
                        populate_discography(artist)
                    end
                end
            end

            # Populates the discography of an artist entity.
            def self.populate_discography artist
                Services::Musicbrainz::MusicbrainzAlbum.save_and_filter_album_list artist, find_remote(artist).release_groups
            end

            def self.find_or_create_by_names_list artist_names
                list = Array.new
                artist_names.each do |name|
                    artist = find_or_create_by_name name
                    list << artist unless artist.id.nil?
                end
                list
            end

            def self.find_or_create_by_name artist_name
                log "Find or create artist named '#{artist_name}'"
                find_or_create find_remote_by_name artist_name
            end

            # Finds or creates Artist entities from standard Musicbrainz API return data.
            def self.find_or_create mb_artist
                unless mb_artist.nil? or mb_artist.id.nil?
                    log "Find or create artist with :mbid => #{mb_artist.id}"
                    Artist.where(mbid: mb_artist.id).first_or_create! do |artist|
                        warn "Artist '#{mb_artist.name}' (#{mb_artist.id}) did not exist."
                        log "Creating '#{mb_artist.name}' (#{mb_artist.id})"
                        artist.name = mb_artist.name
                    end
                end
            end

            protected

            # Finds an artist using the MusicBrainz API using
            # the known fields in the artist entity.
            def self.find_remote artist
                log "'find_by_name(#{artist.name})'"
                find_remote_by_name(artist.name) unless artist.name.nil?
            end

            # Finds an artist using the MusicBrainz API using
            # the known fields in the artist entity.
            def self.find_remote_by_name name
                log "'find_remote_by_name(#{name})'"
                MusicBrainz::Artist.find_by_name(name)
            end

        end
    end
end
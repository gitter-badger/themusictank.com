module Services
    module Musicbrainz
    # This class talks to the Musicbrainz API and generates
        # artist entities from the API results
        class MusicbrainzArtist < Services::Musicbrainz::Base

            # Populates the artists with empty discographies by querying MusicBrainz' database.
            def self.populate_empty_discographies
                Artist.find_with_empty_discographies.each do |artist|
                    populate_discography(artist)
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
                        artist.last_mb_update = 1.month.ago
                    end
                end
            end

            protected

            # Finds an artist using the MusicBrainz API using
            # the known fields in the artist entity.
            def self.find_remote artist
                log "'find_by_name(#{artist.name})'"
                MusicBrainz::MusicbrainzArtist.find_by_name(artist.name) unless artist.name.nil?
            end

            # Finds an artist using the MusicBrainz API using
            # the known fields in the artist entity.
            def self.find_remote_by_name name
                log "'find_remote_by_name(#{name})'"
                MusicBrainz::MusicbrainzArtist.find_by_name(name)
            end

        end
    end
end

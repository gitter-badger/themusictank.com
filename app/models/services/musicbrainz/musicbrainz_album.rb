module Services
    module Musicbrainz
        # This class talks to the Musicbrainz API and generates
        # artist entities from the API results
        class MusicbrainzAlbum < Services::Musicbrainz::Base

            # Populates the albums with empty tracks by querying MusicBrainz' database.
            def self.populate_trackless
                Album.find_with_no_tracks.limit(600).each do |album|
                    populate_trackless_album album
                end
            end

            def self.populate_trackless_album album
                log "Populating (ID: #{album.id}) #{album.title}."
                track_list = find_remote_tracks album
                unless track_list.nil?
                    Services::Musicbrainz::MusicbrainzTrack.save_and_filter_track_list album, track_list
                else
                    warn "Did not receive a track listing."
                end
            end

            # Finds or creates Album entities from standard Musicbrainz API return data.
            def self.find_or_create mb_release
                unless mb_release.id.nil?
                    log "Find or create :mbid => #{mb_release.id}"
                    Album.where(mbid: mb_release.id).first_or_create! do |album|
                        warn "Album '#{mb_release.title}' (#{mb_release.id}) did not exist."
                        log "Creating '#{mb_release.title}' (#{mb_release.id})"
                        album.title = mb_release.title

                        begin
                            album.release_date = mb_release.first_release_date.to_datetime
                        rescue
                            warn "Failed to parse the release date of the album."
                            album.release_date = nil
                        end

                    end
                end
            end

            # Creates a list of albums and associates them to an artist
            def self.save_and_filter_album_list artist, release_groups
                if artist.albums.nil?
                    artist.albums = Array.new
                end

                # Find or create the list of albums based on the releases
                # liked to the release group.
                release_groups.each do |release|
                    album = find_or_create(release)
                    artist.albums << album unless album.id.nil?
                end


                # We have updated MusicBrainz, save the flag.
                artist.last_mb_update = DateTime.now
                artist.save!

                # Return the album list.
                artist.albums
            end

            protected

            # Finds an artist using the MusicBrainz API using
            # the known fields in the album entity.
            def self.find_remote album
                log "'find_by_artist_and_title(#{album.artist.name}, #{album.title})'"
                MusicBrainz::ReleaseGroup.find_by_artist_and_title(album.artist.name, album.title) unless album.title.nil?
            end

            def self.find_remote_tracks album
                releases = find_remote_releases album
                releases.first.tracks unless releases.nil? or releases.first.nil?
            end

            def self.find_remote_releases album
                remote_album = find_remote(album)
                remote_album.releases unless remote_album.nil?
            end

            def self.find_remote_album album
                find_remote album
            end

        end
    end
end

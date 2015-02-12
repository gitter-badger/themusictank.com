module Services
    module Musicbrainz
        # This class talks to the Musicbrainz API and generates
        # Tracks entities from the API results
        class MusicbrainzTrack < Services::Musicbrainz::Base

            def self.find_or_create mb_data
                unless mb_data.recording_id.nil?
                    log "Find or create: :mbid => #{mb_data.recording_id}"
                    Track.where(:mbid => mb_data.recording_id).first_or_create! do |track|
                        warn "Track '#{mb_data.title}' (#{mb_data.recording_id}) did not exist."
                        log "Creating '#{mb_data.title}' (#{mb_data.recording_id})"
                        track.title = mb_data.title
                        track.position = mb_data.position
                        track.duration = mb_data.length
                    end
                end
            end

            def self.save_and_filter_track_list album, possibletracks
                if album.tracks.nil?
                    album.tracks = Array.new
                end

                possibletracks.each do |trackData|
                    track = Services::Musicbrainz::MusicbrainzTrack.find_or_create trackData
                    album.tracks << track unless track.id.nil?
                end

                album.save!
            end

        end
    end
end

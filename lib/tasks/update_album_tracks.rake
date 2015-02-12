task :update_album_tracks => :environment do
    Rails.logger.info "[RAKE START] update_album_tracks : Fetching albums with no tracks."
    Services::Musicbrainz::MusicbrainzAlbum.populate_trackless
    Rails.logger.info "[RAKE END]   update_album_tracks"
end

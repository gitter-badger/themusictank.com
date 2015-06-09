task :update_album_details => :environment do
    Rails.logger.info "[RAKE START] update_album_details: Updating expired albums."
    Services::Lastfm::LastfmAlbum.update_expired
    Rails.logger.info "[RAKE END]   update_album_details"
end

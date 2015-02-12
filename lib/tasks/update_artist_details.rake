task :update_artist_details => :environment do
    Rails.logger.info "[RAKE START] update_artist_details: Updating expired artists."
    Services::Lastfm::LastfmArtist.update_expired
    Rails.logger.info "[RAKE END]   update_artist_details"
end

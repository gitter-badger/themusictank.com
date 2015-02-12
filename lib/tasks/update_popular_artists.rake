task :update_popular_artists => :environment do
    Rails.logger.info "[RAKE START] update_popular_artists : Fetching new popular artists."
    Services::Lastfm::LastfmArtist.update_tmt_top_artists
    Rails.logger.info "[RAKE END]   update_popular_artists"
end

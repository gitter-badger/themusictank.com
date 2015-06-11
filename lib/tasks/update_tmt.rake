task :update_tmt => :setup_logger do
    Rails.logger.info "[RAKE START] update_tmt: Updating everything."
    Rake::Task[:update_artist_details].invoke
    Rake::Task[:update_album_details].invoke
    Rake::Task[:update_album_tracks].invoke
    Rake::Task[:update_thumbnails].invoke
    Rails.logger.info "[RAKE END]   update_tmt"
end

task :update_track_soundwave => :setup_logger do
    Rails.logger.info "[RAKE START] update_track_soundwaves"
    Services::Youtube::YoutubeAnalyser.run
    Rails.logger.info "[RAKE END]   update_track_soundwaves"
end

task :update_thumbnails => :environment do
    Rails.logger.info "[RAKE START] update_thumbnails."
    Rails.logger.info "Updating artist thumbnails."
    Services::S3::ArtistThumbnail.generate_thumbnails
    Rails.logger.info "[RAKE END]   update_thumbnails"
end

task :update_thumbnails => :environment do
    Rails.logger.info "[RAKE START] update_thumbnails."
    Rails.logger.info "Updating artist thumbnails."
    Services::S3::S3ArtistThumbnail.generate_thumbnails
    Rails.logger.info "Updating album thumbnails."
    Services::S3::S3AlbumThumbnail.generate_thumbnails
    Rails.logger.info "[RAKE END]   update_thumbnails"
end

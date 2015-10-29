module Services
    module S3
        # This class talks to the S3 API and generates
        # album thumbnails
        class S3AlbumThumbnail < Services::S3::S3Thumbnail
            def self.generate_thumbnails
                Album.with_no_thumbnail.limit(100).each do |album|
                    generate_thumbnail album
                end
            end
        end
    end
end

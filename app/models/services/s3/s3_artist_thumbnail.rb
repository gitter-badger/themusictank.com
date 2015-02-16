module Services
    module S3
        # This class talks to the S3 API and generates
        # artist thumbnails
        class S3ArtistThumbnail < Services::S3::S3Thumbnail

            def self.generate_thumbnails
                Artist.with_no_thumbnail.each do |artist|
                    generate_thumbnail artist
                end
            end

        end
    end
end

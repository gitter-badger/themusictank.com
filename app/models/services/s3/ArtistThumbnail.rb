module Services
    module S3
        # This class talks to the S3 API and generates
        # artist thumbnails
        class ArtistThumbnail < Services::S3::Thumbnail

            def generate_thumbnails
                Artist.with_no_thumbnails.each do |artist|
                    generate_thumbnail artist
                end
            end

        end
    end
end

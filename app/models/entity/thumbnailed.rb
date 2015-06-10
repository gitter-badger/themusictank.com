module Entity
    # Represent a release by a musician or band.
    module Thumbnailed

        def get_thumbnail_key
            if slug?
                if slug.length > 1
                    subdirectories = slug[0].downcase + File::SEPARATOR + slug[1].downcase
                else
                    subdirectories = slug[0].downcase + File::SEPARATOR + "_"
                end
                self.class.name.downcase + File::SEPARATOR + subdirectories + File::SEPARATOR + slug
            end
        end

        def get_thumbnail_key_for type
            if slug?
                get_thumbnail_key + "-#{type}.jpg"
            end
        end

        def get_thumbnail_url type = "thumb"
            if self.thumbnail.nil?
                "//static.themusictank.com/assets/images/placeholder.png"
            else
                "//static.themusictank.com/" + get_thumbnail_key_for(type)
            end
        end

    end
end

module Services
    module S3
        # This class talks to the S3 API
        class S3Thumbnail < Services::Base

            require 'open-uri'
            require 's3'
            require 'tmpdir'

            IMAGE_SIZES = {
                :mobile_thumb => 150,
                :thumb => 300,
                :mobile_big => 400,
                :big   => 900,
                :mobile_blur => 400,
                :blur  => 900
            }

            # Generates a thumbnail for an entity
            def self.generate_thumbnail entity
                delete_thumbnails_for entity
                create_thumbnails_for entity
            end

            protected

                def self.delete_thumbnails_for entity
                    if entity.thumbnail?
                        IMAGE_SIZES.each do |key, value|
                            thumbnail_key = entity.get_thumbnail_key_for key
                            delete_bucket_image thumbnail_key
                        end
                    end
                end

                def self.create_thumbnails_for entity
                    if entity.thumbnail_source?
                        @local_working_file = download_remote_image entity.thumbnail_source

                        IMAGE_SIZES.each do |key, value|
                            thumbnail_key = entity.get_thumbnail_key_for key
                            formatted_thumbnail = format_working_file(key, value)
                            send_to_bucket thumbnail_key, formatted_thumbnail
                            File.delete formatted_thumbnail
                        end

                        File.delete @local_working_file
                        @local_working_file = nil

                        entity.update_attributes(:thumbnail => entity.get_thumbnail_key)
                    end
                end

                def self.download_remote_image src
                    log "Downloading '#{src}' to '#{tempdir}local_working_file.jpg'"
                    open(tempdir + 'local_working_file.jpg', 'wb') do |file|
                        file << open(src).read
                    end
                end

                def self.get_client
                    if @client.nil?
                        @client = ::S3::Service.new(
                            :access_key_id => ENV["Amazon_S3_key"],
                            :secret_access_key => ENV["Amazon_S3_secret"]
                        )
                    end
                    @client
                end

                def self.get_bucket
                    if @bucket.nil?
                        @bucket = get_client.buckets.find(ENV["Amazon_S3_bucket"])
                    end
                    @bucket
                end

                def self.delete_bucket_image thumbnail_key
                    image = bucket.objects.find thumbnail_key
                    image.destroy unless image.nil?
                end

                def self.send_to_bucket thumbnail_key, formatted_thumbnail
                    log "Sending #{thumbnail_key} to bucket"
                    remote = get_bucket.objects.build thumbnail_key
                    remote.content = open(formatted_thumbnail)
                    remote.save
                end

                def self.format_working_file type, size
                    save_location = tempdir + "resized.jpg";
                    working_location = to_file_path @local_working_file

                    log "Will save resized file to #{save_location}"

                    # Run imagemagik in the command line as to stay more efficient resources wise.
                    cmd = sprintf("convert %s -resize %d %s", working_location, size, save_location)
                    log cmd
                    system(cmd);

                    # Run images requiring special effects
                    if type.to_s.include? "blur"
                        cmd = sprintf("convert %s -channel RGBA -blur 0x8 %s ", save_location, save_location)
                        log cmd
                        system(cmd);
                    end

                    save_location
                end

                def self.to_file_path file
                    File.dirname(file) + File::SEPARATOR + File.basename(file)
                end

                def self.tempdir
                    Dir.tmpdir + File::SEPARATOR
                end

        end
    end
end

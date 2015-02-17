module Services
    module Youtube
        # This class talks to the S3 API and generates
        # artist thumbnails
        class YoutubeVideo < Services::Base

            require "net/http"
            require "uri"

            def self.get_video_key track
                if Track.youtube_key_is_expired? track
                    response = Net::HTTP.get_response(query_uri(track))
                    track.update_attributes(
                        :youtube_key => parse_key(response.body),
                        :last_youtube_update => DateTime.now
                    ) if response.is_a?(Net::HTTPSuccess)
                end
                track.youtube_key
            end

            protected

                def self.query_uri track
                    uri = URI.parse("http://gdata.youtube.com/feeds/api/videos")
                    uri.query = URI.encode_www_form({
                        :alt => "json",
                        "max-results" => 1 ,
                        :q => "#{track.albums.first.artist.name}-#{track.title}"
                    })
                    uri
                end

                def self.parse_key response
                    decoded = ActiveSupport::JSON.decode response
                    decoded['feed']['entry'].each do |entry|
                        entry['link'].each do |link|
                            link['href'].gsub(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/) { |m|
                                return "#{$2}" unless "#{$2}".empty?
                            }
                        end
                    end
                    nil
                end

        end
    end
end

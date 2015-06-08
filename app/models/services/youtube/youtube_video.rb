module Services
    module Youtube
        # This class talks to Youtube and pulls a valid video match
        class YoutubeVideo < Services::Base

            require "net/http"
            require "uri"

            def self.get_video_key track
                if Track.youtube_key_is_expired? track

                    api_client.search(maxResults: 1, query: build_query_string(track)) do |v|
                        track.update_attributes(
                            :youtube_key => parse_key(v),
                            :last_youtube_update => DateTime.now,
                        )
                    end


                    # response = Net::HTTP.get_response(query_uri(track))
                    # puts response.to_yaml
                    # track.update_attributes(
                    #     :youtube_key => parse_key(response.body),
                    #     :last_youtube_update => DateTime.now,
                    # ) if response.is_a?(Net::HTTPSuccess)
                end
                track.youtube_key
            end

            protected

                def self.api_client
                    Yourub::Client.new({
                        developer_key: ENV["Youtube_key"],
                        youtube_api_version: 'v3',
                        youtube_api_service_name: 'youtube',
                        application_name: 'yourub',
                        application_version: '0.1'
                    })
                end


                def self.build_query_string track
                    "#{track.albums.first.artist.name}-#{track.title}"
                end

                def self.parse_key response
                    response["id"]
                end

                # def self.query_uri track
                #     uri = URI.parse("http://gdata.youtube.com/feeds/api/videos")
                #     uri.query = URI.encode_www_form({
                #         :alt => "json",
                #         "max-results" => 1 ,
                #         :key => ENV['Youtube_key'],
                #         :q => "#{track.albums.first.artist.name}-#{track.title}"
                #     })
                #     uri
                # end

                # def self.parse_key response
                #     decoded = ActiveSupport::JSON.decode response
                #     decoded['feed']['entry'].each do |entry|
                #         entry['link'].each do |link|
                #             link['href'].gsub(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/) { |m|
                #                 return "#{$2}" unless "#{$2}".empty?
                #             }
                #         end
                #     end
                #     nil
                # end

        end
    end
end

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

        end
    end
end

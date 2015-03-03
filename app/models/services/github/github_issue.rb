module Services
    module Github

        class GithubReport < Struct.new(:title, :body, :labels)
            def assignee
                "francoisfaubert"
            end
        end


        # This class talks to Github's API
        # and logs in an issue
        class GithubIssue < Services::Base

            # Documentation on this can be found there
            # https://developer.github.com/v3/issues/#edit-an-issue

            require "net/http"
            require "uri"

            def user_bug_report data
                #POST /repos/:owner/:repo/issues

                report = GithubReport.new(to_title(data), to_summary(data), to_labels(data))
                #response = Net::HTTP.get_response(query_uri(report))
            end


            # def self.get_video_key track
            #     if Track.youtube_key_is_expired? track
            #         response = Net::HTTP.get_response(query_uri(track))
            #         track.update_attributes(
            #             :youtube_key => parse_key(response.body),
            #             :last_youtube_update => DateTime.now
            #         ) if response.is_a?(Net::HTTPSuccess)
            #     end
            #     track.youtube_key
            # end

            protected

                def self.to_summary data
                    "Type: #{data['type']}<br>Location: #{data['location']}"
                end

                def self.to_title data
                    title = "Automated bug report"

                    unless @current_user.nil?
                        title += " by #{@current_user.name} (#{@current_user.id})"
                    end

                    title
                end

                def self.to_labels data
                    labels = Array.new
                    labels << "user submitted"
                    labels << data['type']

                    {"labels" : labels}
                end

                # def self.query_uri track
                #     uri = URI.parse("https://api.github.com/")
                #     uri.query = URI.encode_www_form({
                #         :alt => "json",
                #         "max-results" => 1 ,
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

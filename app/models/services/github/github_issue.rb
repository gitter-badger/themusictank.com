module Services
    module Github

        class GithubReport < Struct.new(:title, :body, :labels)
            def assignee
                "francoisfaubert"
            end

            def to_map
                map = Hash.new
                self.members.each { |m| map[m] = self[m] }
                map
            end

            def to_json(*a)
               to_map.to_json(*a)
            end
        end

        # This class talks to Github's API
        # and logs in an issue
        class GithubIssue < Services::Base

            # Documentation on this can be found there
            # https://developer.github.com/v3/issues/#edit-an-issue

            def self.create_automated data
                github.issues.create(report(data).to_map)
            end

            def self.update_automated data
                github.issues.edit(ENV['Github_username'], ENV['Github_repo'], data['report_number'], report(data).to_map)
            end

            protected

                def self.report data
                    GithubReport.new(to_title(data), to_summary(data), to_labels(data))
                end

                def self.github
                    ::Github.new basic_auth: "#{ENV['Github_username']}:#{ENV['Github_password']}", user: "#{ENV['Github_username']}", repo: "#{ENV['Github_repo']}"
                end

                def self.to_summary data
                    "Type: #{data['iden']}<br>Location: #{data['location']}<br>Details:<br> #{data['details']}"
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
                    labels << data['iden']
                    labels
                end

                def self.build_post report
                    uri = URI.parse("https://api.github.com/repos/francoisfaubert/themusictank/issues")
                    uri.query = URI.encode_www_form(report.to_map)
                    uri
                end
        end
    end
end

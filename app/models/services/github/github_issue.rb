module Services
    module Github

        # This class is a structure matching the issues on github.
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
        #
        # Documentation on this can be found there
        # https://developer.github.com/v3/issues/#edit-an-issue
        class GithubIssue < Services::Base


            # Creates an automated bug report and sends it
            # on the Github repo.
            def self.create_automated data
                github.issues.create(report(data).to_map)
            end

            # Updates an existing bug report and updates it
            # on the Github repo.
            def self.update_automated data
                github.issues.edit(ENV['Github_username'], ENV['Github_repo'], data['report_number'], report(data).to_map)
            end

            protected

                # Generates a bug report based on known post values.
                def self.report data
                    GithubReport.new(to_title(data), to_summary(data), to_labels(data))
                end

                # Returns a Github connection.
                def self.github
                    ::Github.new basic_auth: "#{ENV['Github_username']}:#{ENV['Github_password']}", user: "#{ENV['Github_username']}", repo: "#{ENV['Github_repo']}"
                end

                # Generates a bug summary from data
                def self.to_summary data
                    "Type: #{data['iden']}<br>Location: #{data['location']}<br>Details:<br> #{data['details']}"
                end

                # Generates a bug title from data
                def self.to_title data
                    title = "Automated bug report"

                    unless @current_user.nil?
                        title += " by #{@current_user.name} (#{@current_user.id})"
                    end

                    title
                end

                # Generates bug tags from data.
                def self.to_labels data
                    labels = Array.new
                    labels << "user submitted"
                    labels << data['iden']
                    labels
                end
        end
    end
end

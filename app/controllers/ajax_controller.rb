class AjaxController < ApplicationController

    def artist_search
        formatted_results = Array.new

        Artist.search(params[:q]).each do |artist|
            formatted_results << {
                "slug" => artist.slug,
                "artist" => artist.name
            }
        end

        render :json => formatted_results
    end

    def track_search
        formatted_results = Array.new

        Track.search(params[:q]).each do |track|
            formatted_results << {
                "slug" => track.slug,
                "track" => track.title,
                "album" => track.albums[0].title,
                "artist" => track.albums[0].artist.name
            }
        end

        render :json => formatted_results
    end

    def album_search
        formatted_results = Array.new

        Album.search(params[:q]).each do |album|
            formatted_results << {
                "slug" => album.slug,
                "album" => album.title,
                "artist" => album.artist.name,
            }
        end

        render :json => formatted_results
    end

    # Returns the information of the youtube video required
    # to play a requested song.
    def yt_key
        track = Track.find_by(:slug => params[:slug]) or not_found
        render :json => {
            "youtube_key" => Services::Youtube::YoutubeVideo.get_video_key(track)
        }
    end

    # Marks user notifications as 'read'
    def okstfu
        UserActivity.mark_user_notifications_read current_user
        redirect_to action: :whatsup
    end

    # Checks for user notifications
    # TODO: This could be pushed instead
    def whatsup
        @notifications = UserActivity.find_notifications_for_user(current_user).limit(5)
        render layout: false
    end


    def bugreport
        # https://developer.github.com/v3/issues/#edit-an-issue

        Services::Github::GithubIssue.user_bug_report params

        render layout: false

        # if ($this->request->is('post'))
        # {
        #     $BugsTable = TableRegistry::get('Bugs');
        #     $bug = $BugsTable->newEntity($this->request->data);
        #     if ($BugsTable->save($bug)) {
        #         $this->set("bug", $bug);
        #     }
        #     $this->render("bugreport");
        # }
    end

end

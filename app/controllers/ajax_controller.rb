class AjaxController < ApplicationController

    def artist_search
        formatted_results = Array.new

        Artist.search(query_params[:q]).each do |artist|
            formatted_results << {
                "slug" => "/artists/" + artist.slug,
                "artist" => artist.name
            }
        end

        render :json => formatted_results
    end

    def track_search
        formatted_results = Array.new

        Track.search(query_params[:q]).each do |track|
            formatted_results << {
                "slug" => "/tracks/" + track.slug,
                "track" => track.title,
                "album" => track.albums[0].title,
                "artist" => track.albums[0].artist.name
            }
        end

        render :json => formatted_results
    end

    def album_search
        formatted_results = Array.new

        Album.search(query_params[:q]).each do |album|
            formatted_results << {
                "slug" => "/albums/" + album.slug,
                "album" => album.title,
                "artist" => album.artist.name,
            }
        end

        render :json => formatted_results
    end

    # Returns the information of the youtube video required
    # to play a requested song.
    def yt_key
        track = Track.find_by(:slug => slug_params[:slug]) or not_found
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
        # GET mode means the template has been loaded.
        # Awaiting user confirmation using POST
        if request.post?
            # Check whether we are updating an issue or creating one
            if params.include?("report_number")
                @report = Services::Github::GithubIssue.update_automated(bug_params).body
            else
                @report = Services::Github::GithubIssue.create_automated(bug_params).body
            end
        end
        render layout: false
    end

    def reviewer
        @album = Album.find_by_slug(reviewer_album_params[:slug]) or not_found
        @track = Track.find_by_slug(reviewer_track_params[:slug]) or not_found
        render layout: false
    end

    protected

    def bug_params
        params.permit(:report_number, :iden, :location, :details)
    end

    def slug_params
        params.permit(:slug)
    end

    def query_params
        params.permit(:q)
    end

    def reviewer_track_params
        params.require(:track).permit(:slug)
    end

    def reviewer_album_params
        params.require(:album).permit(:slug)
    end
end

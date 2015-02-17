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

end

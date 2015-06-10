class TracksController < ApplicationController
    def show
        @track = Track.find_by_slug(params[:id]) or not_found

        @meta = {
            "oembed_obj"    => @track,
            "title"         => @track.meta_title,
            "description"   => @track.meta_description
        }
    end

    def search

    end
end

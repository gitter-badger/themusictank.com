class TracksController < ApplicationController
    def view
        @track = Track.find_by_slug(params[:slug]) or not_found

        @meta = {
            "oembed_obj"    => @track,
            "title"         => @track.meta_title,
            "description"   => @track.meta_description
        }
    end

    def search

    end
end

class TracksController < ApplicationController
    def view
        @track = Track.find_by_slug(params[:slug]) or not_found
    end

    def search
    end
end

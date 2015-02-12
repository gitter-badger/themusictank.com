class TracksController < ApplicationController
    def view
        @track = Track.find_by_slug(params[:slug])
    end

    def search
    end
end

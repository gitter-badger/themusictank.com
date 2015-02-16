class SearchController < ApplicationController
    def index
        if request.get?
            @artists = Artist.search(params[:q])
            @albums = Album.search(params[:q])
            @tracks = Track.search(params[:q])
        end
    end
end

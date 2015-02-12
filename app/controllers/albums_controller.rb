class AlbumsController < ApplicationController
    def view
        @album = Album.find_by_slug(params[:slug])
    end

    def search
    end
end

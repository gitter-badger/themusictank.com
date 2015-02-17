class AlbumsController < ApplicationController
    def view
        @album = Album.find_by_slug(params[:slug]) or not_found
    end

    def search
    end
end

class ArtistsController < ApplicationController
    def index
        @artists = Artist.find_random_popular 17
    end

    def view
        @artist = Artist.find_by_slug(params[:slug])
    end

    def search
    end
end

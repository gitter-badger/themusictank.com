class ArtistsController < ApplicationController
    def index
        @artists = Artist.find_random_popular 17
        @featured_artist = @artists.pop
    end

    def view
        @artist = Artist.find_by_slug(params[:slug]) or not_found
    end

    def search
    end
end

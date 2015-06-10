class ArtistsController < ApplicationController
    def index
        @artists = Artist.find_random_popular 17
        @featured_artist = @artists.pop
    end

    def show
        @artist = Artist.find_by_slug(params[:id]) or not_found

        @meta = {
            "oembed_obj"    => @artist,
            "title"         => @artist.meta_title,
            "description"   => @artist.meta_description
        }
    end

    def search
    end
end

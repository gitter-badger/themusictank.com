class ArtistsController < ApplicationController

    before_filter :load_object, :except => [:search, :index]

    after_filter :setup_object_meta, :except => [:search, :index]

    layout "embed", :only => [:embed]

    def index
        @artists = Artist.find_random_popular 17
        @featured_artist = @artists.pop
    end

    def show

    end

    def search

    end

    def embed

    end

    private

    def load_object
        @artist = Artist.find_by_slug(params[:id]) or not_found
    end

    def setup_object_meta
        @meta = {
            "oembed_obj"    => @artist,
            "title"         => @artist.meta_title,
            "description"   => @artist.meta_description
        }
    end
end

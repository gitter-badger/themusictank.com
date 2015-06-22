class AlbumsController < ApplicationController

    before_filter :load_object, :except => [:search]

    after_filter :setup_object_meta, :except => [:search]

    layout "embed", :only => [:embed]

    def show

    end

    def search

    end

    def embed

    end

    private

    def load_object
        @album = Album.find_by_slug(params[:id]) or not_found
    end

    def setup_object_meta
        @meta = {
            "oembed_obj"    => @album,
            "title"         => @album.meta_title,
            "description"   => @album.meta_description
        }
    end

end

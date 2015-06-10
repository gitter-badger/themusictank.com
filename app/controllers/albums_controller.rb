class AlbumsController < ApplicationController
    def show
        @album = Album.find_by_slug(params[:id]) or not_found

        @meta = {
            "oembed_obj"    => @album,
            "title"         => @album.meta_title,
            "description"   => @album.meta_description
        }
    end

    def search
    end
end

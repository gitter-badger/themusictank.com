class AlbumsController < ApplicationController
    def view
        @album = Album.find_by_slug(params[:slug]) or not_found

        @meta = {
            "oembed_obj"    => @album,
            "title"         => @album.meta_title,
            "description"   => @album.meta_description
        }
    end

    def search
    end
end

class TracksController < ApplicationController

    before_filter :load_objects, :setup_object_meta, :except => [:search]

    layout "embed", :only => [:embed]

    def show

    end

    def search

    end

    def embed

    end

    private

    def load_objects
        @track = Track.find_by_slug(params[:id]) or not_found
        @version = Album.find_by_slug(params[:version]) or not_found unless params[:version].nil?
    end

    def setup_object_meta
        @meta = {
            "oembed_obj"    => @track,
            "title"         => @track.meta_title,
            "description"   => @track.meta_description
        }
    end


end

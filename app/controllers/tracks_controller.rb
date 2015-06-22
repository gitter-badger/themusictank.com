class TracksController < ApplicationController

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
        @track = Track.find_by_slug(params[:id]) or not_found
    end

    def setup_object_meta
        @meta = {
            "oembed_obj"    => @track,
            "title"         => @track.meta_title,
            "description"   => @track.meta_description
        }
    end


end

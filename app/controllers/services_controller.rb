class ServicesController < ApplicationController

    def oembed
        target = params[:url] or not_found
        return head(501) unless params[:format].nil? or params[:format].downcase == "json"

        @object = nil
        case target
            when /\/albums\/(.*)\/?/
                @object = Album.find_by_slug("#{$1}") or not_found
            when /\/artists\/(.*)\/?/
                @object = Artist.find_by_slug("#{$1}") or not_found
            when /\/tracks\/(.*)\/?/
                @object = Track.find_by_slug("#{$1}") or not_found
        end

        not_found if @object.nil?

        render :json => @object.to_oembed_hash.to_json
    end

end

class ProfilesController < ApplicationController

    before_filter :require_login

    def dashboard

    end

    def view
        @profile = User.find_by_slug(params[:slug]) or not_found
    end

end

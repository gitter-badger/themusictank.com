class ProfilesController < ApplicationController

    before_filter :require_login

    def dashboard

    end

    def view
        @profile = User.find_by_slug(params[:slug]) or not_found
    end

    def edit
        @profile = User.find_by_id(current_user.id) or not_found
    end

    def update
        @profile = User.find_by_id(current_user.id) or not_found
        @profile.update_attributes user_params
        reload_current_user
        render :action => :edit
    end

    private

    def user_params
        params.require(:user).permit(:name, :email, :slug)
    end

end

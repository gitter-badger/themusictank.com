class UsersController < ApplicationController
    before_filter :require_login,  only: [:edit]

    def edit

    end

end

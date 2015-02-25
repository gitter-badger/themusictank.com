class NotificationsController < ApplicationController

    before_filter :require_login

    def index
        @notifications = Notification.find_paginated_for_user(current_user.id, params[:page])
    end

end

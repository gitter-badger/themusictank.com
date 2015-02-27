class NotificationsController < ApplicationController

    before_filter :require_login

    def index
        @notifications = UserActivity.find_paginated_notifications_for_user(current_user, params)
    end

end

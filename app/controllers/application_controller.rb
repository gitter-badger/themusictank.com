class ApplicationController < ActionController::Base
    # Prevent CSRF attacks by raising an exception.
    # For APIs, you may want to use :null_session instead.
    protect_from_forgery with: :exception

    def not_found
        raise ActionController::RoutingError.new('Not Found')
    end

    def require_login
        unless user_signed_in?
          flash[:error] = "Please login or create a new user before reaching your dashboard."
          redirect_to login_url
        else
          #  @current_user = current_user
        end
    end
end

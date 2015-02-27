class ApplicationController < ActionController::Base
    # Prevent CSRF attacks by raising an exception.
    # For APIs, you may want to use :null_session instead.
    protect_from_forgery with: :exception

    before_filter :current_user

    def not_found
        raise ActionController::RoutingError.new('Not Found')
    end

    def require_login
        puts "da fak"
        puts user_signed_in?

        unless user_signed_in?
          flash[:error] = "Please login or create a new user before reaching your dashboard."
          redirect_to controller: :sessions, action: :login
        end
    end

    def reload_current_user
        @current_user = User.find(session[:user_id]) if session[:user_id]
    end

    private

        def user_signed_in?
            !@current_user.nil?
        end

        def current_user
            @current_user ||= User.find(session[:user_id]) if session[:user_id] && User.exists?(session[:user_id])
        end

        helper_method :current_user
end

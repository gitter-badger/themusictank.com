class SessionsController < ApplicationController
  def create

    provider = env["omniauth.auth"][:provider]
    uid = env["omniauth.auth"][:uid]

    user = User.from_omniauth(provider, uid, env["omniauth.auth"])
    session[:user_id] = user.id
    redirect_to root_url
  end

  def destroy
    session[:user_id] = nil;
    redirect_to root_url
  end

  def failure
      flash[:error] = "We could not log you in."
      redirect_to controller: :sessions, action: :login
  end

  def login

  end


end

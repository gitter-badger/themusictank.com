class SessionsController < ApplicationController
  def create
    provider = env["omniauth.auth"][:provider]
    uid = env["omniauth.auth"][:uid]

    user = Services::Omniauth::OmniauthUser.find_or_create(provider, uid, env["omniauth.auth"])
    session[:user_id] = user.id

    if params[:rurl].nil?
        redirect_to root_url
    end

    # I don't think this works because we probably lose the param once we forward to Facebook
    redirect_to params[:rurl]
  end

  def destroy
    session[:user_id] = nil;
    redirect_to root_url
  end

  def failure
      flash[:error] = t "We could not log you in."
      redirect_to controller: :sessions, action: :login
  end

  def login

  end

end



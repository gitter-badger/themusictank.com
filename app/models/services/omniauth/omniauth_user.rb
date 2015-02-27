module Services
    module Omniauth
        # This class talks to the Omniauth API
        class OmniauthUser < Services::Base

            def self.find_or_create(provider, uid, auth)
                User.where(:provider => provider, :uid => uid).first_or_initialize.tap do |user|
                  user.provider = auth.provider
                  user.uid = auth.uid
                  user.name = auth.info.name
                  user.email = auth.info.email
                  user.oauth_token = auth.credentials.token
                  user.oauth_expires_at = Time.at(auth.credentials.expires_at)
                  user.save!
                end
            end

        end
    end
end

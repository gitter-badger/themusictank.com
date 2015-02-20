module Repository
    # This class handles queries of Track objects.
    module Users
        include Repository::Behavior::Slugged

        def from_omniauth(provider, uid, auth)
            where(:provider => provider, :uid => uid).first_or_initialize.tap do |user|
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

module Entity
    # Represent a release by a musician or band.
    class User < Entity::Slugged
        include Entity::Thumbnailed

        self.abstract_class = true

        def apply_omniauth(auth)
          # In previous omniauth, 'user_info' was used in place of 'raw_info'
          self.email = auth['extra']['raw_info']['email']
          # Again, saving token is optional. If you haven't created the column in authentications table, this will fail
          authentications.build(:provider => auth['provider'], :uid => auth['uid'], :token => auth['credentials']['token'])
        end

    end
end

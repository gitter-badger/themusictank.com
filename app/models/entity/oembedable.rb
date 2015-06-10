module Entity
    # Represent a release by a musician or band.
    module Oembedable

        def link_back
            "/" + self.class.name + "/view/" + self.slug
        end

    end
end

module Repository
    module Behavior
        # This class handles queries of thumbnailed objects.
        module Thumbnailed
            include Repository::Base

            def with_no_thumbnail
                where(:thumbnail => nil)
            end

        end
    end
end

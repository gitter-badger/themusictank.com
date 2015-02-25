module Repository
    # This class handles queries of Track objects.
    module Notifications

        def find_paginated_for_user id, page
            where(:user_id => id).paginate(:page => page).per_page(15)
        end

    end
end

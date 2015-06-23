module Repository
    module Behavior
        # This class handles search queries
        module Searchable
            include Repository::Base

            # http://stackoverflow.com/questions/22435780/how-to-order-results-by-closest-match-to-query
            def search criteria, limit = 10
                regexp = /#{criteria}/i;
                result = search_order.search_where(criteria).limit(limit)
                result.sort{|x, y| (x =~ regexp) <=> (y =~ regexp) }
            end

            def search_order
                if self.respond_to? 'search_order_field'
                    order(self.search_order_field)
                else
                    order(:title)
                end
            end

            def search_where criteria
                where_param = "title ILIKE ?"
                if self.respond_to? 'search_where_field'
                    where_param = self.search_where_field + " ILIKE ?"
                end
                where(where_param, "%#{criteria}%")
            end

        end
    end
end

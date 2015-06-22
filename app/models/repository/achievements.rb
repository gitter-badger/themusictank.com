module Repository
    module Achievements
        include Repository::Base

        def find_most_popular
            build_popularity_resultset find_most_popular_set
        end

        def find_least_popular
            build_popularity_resultset find_least_popular_set
        end

        def find_by_slug slug
            Services::TMTAchievement::Base.factory_by_slug(slug)
        end

        def count_rewardees slug
            find_popularity_set.where(:slug => slug).first[:count]
        end

        private

        def build_popularity_resultset records
            resultset = Array.new

            records.each do |row|
                resultset << {
                    :rewards        => row.count.to_i,
                    :achievement    => find_by_slug(row.slug)
                }
            end

            resultset
        end

        def find_most_popular_set
            find_popularity_set
        end

        def find_least_popular_set
            find_popularity_set.order("count ASC")
        end

        def find_popularity_set
            select("count(id), slug").order("count DESC").group( :slug ).limit(10)
        end

    end
end

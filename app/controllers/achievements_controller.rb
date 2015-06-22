class AchievementsController < ApplicationController

    before_filter :load_user_total

    def index
        @mostPopular = Achievement.find_most_popular
        @leastPopular = Achievement.find_least_popular
    end

    def show
        @achievement = Achievement.find_by_slug(params[:id]) or not_found
        @meta = {
            "oembed_obj"    => @achievement,
            "title"         => @achievement.meta_title,
            "description"   => @achievement.meta_description
        }
    end

    private

    def load_user_total
        @totalUsers = User.count
    end
end

require 'rails_helper'
require 'faker'

RSpec.describe UserActivity, type: :model do
    FactoryGirl.define do
        factory :user_activity do |f|
            f.title { Faker::Lorem.sentences(1, true) }
            f.linked_obj_type { "Services::TMTAchievement::CreatedAccount" }
            f.must_notify_user { 1 }
        end
    end
end

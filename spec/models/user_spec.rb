require 'rails_helper'

RSpec.describe User, type: :model do
    FactoryGirl.define do
        factory :user do |f|
            f.email { Faker::Internet.email }
            f.slug { Faker::Internet.user_name(f.name, %w(. _ -)) }
            f.provider { "facebook" }
            f.name { Faker::Name.name }
        end
    end
end

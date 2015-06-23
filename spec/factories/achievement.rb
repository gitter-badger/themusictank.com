require 'faker'

FactoryGirl.define do
    factory :achievement do |f|
        f.slug { "created_account" }
        f.count { 10 }
    end
end

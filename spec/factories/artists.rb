require 'faker'

FactoryGirl.define do
    factory :artist do |f|
        f.name { Faker::Name.first_name }
        f.bio { Faker::Lorem.paragraph(2) }
        f.slug { Faker::Internet.user_name(f.name, %w(. _ -)) }
        f.thumbnail_source { Faker::Company.logo }
        f.is_popular { false }
        f.mbid { "cc197bad-dc9c-440d-a5b5-d52ba2e14234" }
    end
end

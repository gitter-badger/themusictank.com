require 'faker'

FactoryGirl.define do
    factory :track do |f|
        f.title { Faker::Name.first_name }
        f.slug { Faker::Internet.user_name(f.name, %w(. _ -)) }
        f.mbid { "cc197bad-dc9c-440d-a5b5-d52ba2e14234" }
        f.duration { 16000 }
        f.position  1
    end
end

require 'faker'

FactoryGirl.define do
    factory :album do |f|
        f.title { Faker::Name.first_name }
        f.slug { Faker::Internet.user_name(f.name, %w(. _ -)) }
        f.thumbnail_source { Faker::Company.logo }
        f.mbid { "cc197bad-dc9c-440d-a5b5-d52ba2e14234" }
        f.release_date { Faker::Date.between(2.days.ago, Date.today) }
        f.last_lastfm_update { Faker::Date.between(2.days.ago, Date.today) }
    end
end

require 'rails_helper'

RSpec.describe Track, type: :model do

    before(:all) {
        @track = FactoryGirl.create(:track)
    }

    it "has a valid factory" do
        expect(@track).to be_valid
    end

    it "is invalid without a Musicbrainz ID" do
        expect(FactoryGirl.build(:track, mbid: nil)).not_to be_valid
    end

    it "is invalid without a slug" do
        expect(FactoryGirl.build(:track, slug: nil)).not_to be_valid
    end

    it "should not allow duplicate MBIDs" do
        expect(FactoryGirl.build(:track, mbid: @track.mbid)).not_to be_valid
    end

    it "should not allow duplicate slugs" do
        expect(FactoryGirl.build(:track, slug: @track.slug)).not_to be_valid
    end

    it "generates a valid slug from that name" do
        expect(@track.generate_slug).to eq(@track.slug)
    end

    it "generates a valid slug from a strange title" do
        track = FactoryGirl.build(:track, title: "&- ?%")
        expect(track.generate_slug).to eq(track.unsupported_length_replacement)
    end

end

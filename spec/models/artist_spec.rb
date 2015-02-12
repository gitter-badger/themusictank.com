require 'rails_helper'

RSpec.describe Artist, type: :model do

    before(:all) {
        @artist = FactoryGirl.create(:artist)
    }

    it "has a valid factory" do
        expect(@artist).to be_valid
    end

    it "is invalid without a Musicbrainz ID" do
        expect(FactoryGirl.build(:artist, mbid: nil)).not_to be_valid
    end

    it "is invalid without a slug" do
        expect(FactoryGirl.build(:artist, slug: nil)).not_to be_valid
    end

    it "should not allow duplicate MBIDs" do
        expect(FactoryGirl.build(:artist, mbid: @artist.mbid)).not_to be_valid
    end

    it "should not allow duplicate slugs" do
        expect(FactoryGirl.build(:artist, slug: @artist.slug)).not_to be_valid
    end

    it "generates a valid slug from that name" do
        expect(@artist.generate_slug).to eq(@artist.slug)
    end

    it "generates a valid slug from a strange name" do
        artist = FactoryGirl.build(:artist, name: "&- ?%")
        expect(artist.generate_slug).to eq(artist.unsupported_length_replacement)
    end

end

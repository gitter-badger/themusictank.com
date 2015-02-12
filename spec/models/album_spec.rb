require 'rails_helper'

RSpec.describe Album, type: :model do

    before(:all) {
        @album = FactoryGirl.create(:album)
    }

    it "has a valid factory" do
        expect(@album).to be_valid
    end

    it "is invalid without a Musicbrainz ID" do
        expect(FactoryGirl.build(:album, mbid: nil)).not_to be_valid
    end

    it "is invalid without a slug" do
        expect(FactoryGirl.build(:album, slug: nil)).not_to be_valid
    end

    it "should not allow duplicate MBIDs" do
        expect(FactoryGirl.build(:album, mbid: @album.mbid)).not_to be_valid
    end

    it "should not allow duplicate slugs" do
        expect(FactoryGirl.build(:album, slug: @album.slug)).not_to be_valid
    end

    it "generates a valid slug from that name" do
        expect(@album.generate_slug).to eq(@album.slug)
    end

    it "generates a valid slug from a strange title" do
        album = FactoryGirl.build(:album, title: "&- ?%")
        expect(album.generate_slug).to eq(album.unsupported_length_replacement)
    end

end

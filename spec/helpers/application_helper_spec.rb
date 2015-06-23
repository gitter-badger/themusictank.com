require 'rails_helper'

RSpec.describe ApplicationHelper, type: :helper do

    describe "pct formatter" do
        it "correctly handles numbers" do
            expect(ApplicationHelper.pct(10,100)).to eq(10)
        end
        it "parsed strings" do
            expect(ApplicationHelper.pct("10","100")).to eq(10)
        end
    end

end

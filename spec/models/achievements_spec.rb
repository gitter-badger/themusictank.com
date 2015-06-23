require 'rails_helper'

RSpec.describe Achievement, type: :model do

    before(:all) {
        @achivement = FactoryGirl.create(:achievement)
    }

end

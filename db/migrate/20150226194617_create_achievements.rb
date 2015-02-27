class CreateAchievements < ActiveRecord::Migration
  def change
    create_table :achievements do |t|
      t.belongs_to :user, index: true
      t.string :slug
      t.integer :count
      t.timestamps
    end
  end
end

class CreateNotifications < ActiveRecord::Migration
  def change
    create_table :notifications do |t|
      t.belongs_to :user, index: true
      t.string :title
      t.string :type
      t.integer :is_viewed
      t.timestamps
    end
  end
end

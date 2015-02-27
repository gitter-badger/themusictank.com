class CreateUserActivities < ActiveRecord::Migration
  def change
    create_table :user_activities do |t|

      t.belongs_to :user, index: true
      t.string :linked_obj_type
      t.integer :linked_obj_id
      t.integer :must_notify_user, default: false

      t.timestamps
    end
  end
end

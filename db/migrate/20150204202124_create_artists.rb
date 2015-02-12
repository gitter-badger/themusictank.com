class CreateArtists < ActiveRecord::Migration
  def change
    create_table :artists do |t|

      t.string :name, :limit => 255, :null => false
      t.string :slug
      t.string :mbid, :limit => 36, :null => false
      t.string :thumbnail_source, :limit => 255
      t.string :thumbnail, :limit => 255
      t.boolean :is_popular

      t.datetime :last_lastfm_update
      t.datetime :last_mb_update
      t.timestamps
    end
    add_index :artists, :slug
  end
end

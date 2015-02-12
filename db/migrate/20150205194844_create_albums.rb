class CreateAlbums < ActiveRecord::Migration
  def change
    create_table :albums do |t|

      t.string :title
      t.string :slug
      t.string :mbid, :limit => 36, :null => false
      t.string :thumbnail_source, :limit => 255
      t.string :thumbnail, :limit => 255
      t.datetime :release_date

      t.belongs_to :artist, index: true

      t.datetime :last_lastfm_update
      t.timestamps
    end
    add_index :albums, :slug
  end
end

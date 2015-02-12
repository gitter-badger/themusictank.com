class CreateTracks < ActiveRecord::Migration
  def change
    create_table :tracks do |t|
      t.string :title
      t.string :slug, unique: true
      t.string :mbid, :limit => 36, :null => false
      t.integer :position
      t.integer :duration

      #t.belongs_to :album, index: true
      t.timestamps
    end
    add_index :tracks, :slug, unique: true
    add_index :tracks, :mbid, unique: true

    create_table :albums_tracks do |t|
        t.references :album
        t.references :track
    end
    add_index :albums_tracks, [:album_id, :track_id]
    add_index :albums_tracks, :track_id
  end
end

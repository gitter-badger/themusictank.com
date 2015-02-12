class AddSimilarArtistsTable < ActiveRecord::Migration
    def change
        create_table :similar_artists, :id => false do |t|
            t.references :artist, :null => false
            t.references :similar_artist, :null => false
        end

        add_index(:similar_artists, [:artist_id, :similar_artist_id], :unique => true)
    end
end

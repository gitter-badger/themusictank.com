class AddYoutubekeyColumnToTracks < ActiveRecord::Migration
  def change
    add_column :tracks, :youtube_key, :string
    add_column :tracks, :last_youtube_update, :datetime
  end
end

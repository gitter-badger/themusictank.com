class CreateTrackSoundwaves < ActiveRecord::Migration
  def change
    create_table :track_soundwaves do |t|
        t.references :track, :null => false
        t.string :soundwave
    end
  end
end

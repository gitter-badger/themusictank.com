class ChangeSoundwaveType < ActiveRecord::Migration
  def change
    change_column :track_soundwaves, :soundwave, :text, :limit => 10485760
  end
end

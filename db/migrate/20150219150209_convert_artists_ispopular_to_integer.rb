class ConvertArtistsIspopularToInteger < ActiveRecord::Migration
  def change
    change_column :artists, :is_popular, :integer, :limit => 1
  end
end

class ConvertArtistsIspopularToInteger < ActiveRecord::Migration
  def change
    change_column :artists, :is_popular, 'integer USING CAST(column_name AS integer)', :limit => 1
  end
end

class AddColumnlastfmplaycountToAlbums < ActiveRecord::Migration
  def change
    add_column :albums, :playcount, :integer
  end
end

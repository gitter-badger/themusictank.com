# Represent a musician or a band
class Artist < Entity::Artist
    extend Repository::Artists

    # How artists are created :
    # There are two ways artists will get created automatically on the website.
    #   - When popular artists are being updated
    #   - When a user searches for an artist.

    # After creation, all fields are expected to be filled out.

    # When the last Musicbrainz update is expired (last_mb_update)
    # we will try and update the discography.

    has_many :albums, -> { order('playcount DESC, release_date ASC') }

    has_and_belongs_to_many :similar_artists, :class_name => "Artist", :join_table => "similar_artists", :association_foreign_key => "similar_artist_id"

    validates :mbid, presence: true, uniqueness: true
end

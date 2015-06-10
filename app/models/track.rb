
# Represent a song on a release
class Track < Entity::Track
    extend Repository::Tracks

    # MusicBrainz ID is unique, but referenced across multiple albums.
    has_many :albums, through: :albums_tracks
    has_many :albums_tracks

    validates :mbid, presence: true, uniqueness: true
end

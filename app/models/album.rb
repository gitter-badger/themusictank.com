# Represent a release by a musician or band.
class Album < Entity::Album
    extend Repository::Albums

    belongs_to :artist
    has_many :tracks, through: :albums_tracks
    has_many :albums_tracks

    validates :mbid, presence: true, uniqueness: true
end

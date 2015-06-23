
# Represent a song on a release
class Track < Entity::Track
    extend Repository::Tracks

    # MusicBrainz ID is unique, but referenced across multiple albums.
    has_many :albums, through: :albums_tracks
    has_many :albums_tracks

    validates :mbid, presence: true, uniqueness: true

    before_save :truncate_title

    def truncate_title
        self.title = self.title[0..254] if self.title.length > 255
    end

end

# Represent a release by a musician or band.
class AlbumsTrack < Entity::AlbumsTrack
    extend Repository::AlbumsTracks

  belongs_to :album
  belongs_to :track

end

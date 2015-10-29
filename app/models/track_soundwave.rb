class TrackSoundwave  < Entity::TrackSoundwave
       extend Repository::TrackSoundwaves

    belongs_to :track
    serialize :soundwave
end

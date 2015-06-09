class TmtController < ApplicationController

    def homepage
        @nbArtists = Artist.count
        @nbAlbums = Album.count
        @nbTracks = Track.count
    end


    def about

    end


    def legal

    end

end

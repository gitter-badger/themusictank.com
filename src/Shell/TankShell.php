<?php

namespace App\Shell;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class TankShell extends Shell {

    public $tasks = [
        "AlbumSnapshotsSync",           "ArtistSnapshotsSync",
        "PopulateAlbumDetails",         "PopulateArtistDetails",
        "PopulateArtistDiscography",    "PopulateTrackDetails",
        "TrackSnapshotsSync",           "TrackWavesSync",
        "UpdatePopularArtists",         "UpdateSongChallenge"
    ];

    public function main()
    {
        $this->out("daily, wavescrawl, twohours");
    }

    public function daily()
    {
        $this->out("DAILY");

        $tracksTbl = TableRegistry::get('Tasks');
        $tracksTbl->setCronStart("daily");

        $this->UpdateSongChallenge->execute();
        $this->ArtistSnapshotsSync->execute();
        $this->AlbumSnapshotsSync->execute();
        $this->TrackSnapshotsSync->execute();

        $tracksTbl->setCronEnd("daily");
    }

    public function wavescrawl()
    {
        $this->out("WAVES CRAWL");
        $this->Config->setCronStart("dailycrawl");

        $this->TrackWavesSync->execute();

        $this->Config->setCronEnd("dailycrawl");
    }

    public function twohours()
    {
        set_time_limit ((HOUR * 2) - 5); // ok bro, you have 1h55 to finish doing your thing
        $this->out("EVERY TWO HOURS");

        $this->Config->setCronStart("twohours");

        // These tasks fail often due to Last.fm's api.
        // Until a noticeable increase in successful responses
        // query them often to make up for failed attempts
        $this->UpdatePopularArtists->execute();
        $this->PopulateArtistDiscography->execute();
        $this->PopulateArtistDetails->execute();
        $this->PopulateAlbumDetails->execute();
        $this->PopulateTrackDetails->execute();
        $this->Config->setCronEnd("twohours");
    }

}

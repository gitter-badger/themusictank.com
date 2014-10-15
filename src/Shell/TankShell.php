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
        $this->out("usage : one of [daily, wavescrawl, twohours]");
    }

    public function daily()
    {
        $this->out("DAILY");

        $tasksTbl = TableRegistry::get('Tasks');
        $tasksTbl->touch("daily.start");

        $this->UpdateSongChallenge->execute();
        $this->ArtistSnapshotsSync->execute();
        $this->AlbumSnapshotsSync->execute();
        $this->TrackSnapshotsSync->execute();

        $tasksTbl->touch("daily.end");
    }

    public function twohours()
    {
        set_time_limit ((HOUR * 2) - 5); // ok bro, you have 1h55 to finish doing your thing
        $this->out("EVERY TWO HOURS");

        $tasksTbl = TableRegistry::get('Tasks');
        $tasksTbl->touch("twohours.start");

        // These tasks fail often due to Last.fm's api.
        // Until a noticeable increase in successful responses
        // query them often to make up for failed attempts
        $this->UpdatePopularArtists->execute();
        $this->PopulateArtistDetails->execute();
        $this->PopulateArtistDiscography->execute();
        $this->PopulateAlbumDetails->execute();

       /*
        $this->PopulateTrackDetails->execute();*/

        $tasksTbl->touch("twohours.end");
    }

    public function wavescrawl()
    {
        $this->out("WAVES CRAWL");

        $tasksTbl = TableRegistry::get('Tasks');
        $tasksTbl->touch("wavescrawl.start");

        $this->TrackWavesSync->execute();

        $tasksTbl->touch("wavescrawl.end");
    }

}

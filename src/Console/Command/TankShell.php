<?php

namespace App\Console\Command;

use Cake\Console\Shell;
use Cake\Log\Log;

use App\Model\Entity\Config;

/**
 * Simple console wrapper around Boris.
 */
class TankShell extends Shell {

    public $tasks = array(
        "AlbumSnapshotsSync",           "ArtistSnapshotsSync",
        "PopulateAlbumDetails",         "PopulateArtistDetails",
        "PopulateArtistDiscography",    "PopulateTrackDetails",
        "TrackSnapshotsSync",           "TrackWavesSync",
        "UpdatePopularArtists",         "UpdateSongChallenge"
    );

    public function daily()
    {
        $this->out("DAILY");


        $tracksTbl = TableRegistry::get('Tasks');//->getBySlug($trackSlug)->first();
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

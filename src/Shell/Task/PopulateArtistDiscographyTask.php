<?php

namespace App\Shell\Task;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Artist;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateArtistDiscographyTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>artist discographies</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('artists_details');


        if ($task->requiresUpdate()) {

            $artistsTbl = TableRegistry::get('Artists');
            $artistsTbl->saveMany($artistsTbl->getWithExpiredDetails());
            $taskTable->touch("popular_artists");
            return;

        } else {
            $this->out("\tArtist discography update is not ready to run.");
        }

/*
        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('popular_artists');

        if ($task->requiresUpdate()) {

            $lastfmApi = new LastfmApi();
            $artists = [];
            foreach ($lastfmApi->getTopArtists() as $artistInfo) {
                $artist = new Artist();
                $artists[] = $artist->loadFromLastFm($artistInfo);
                $artist->lastfm->is_popular = true;
            }

            if (count($artists)) {
                TableRegistry::get('LastfmArtists')->demotePopularArtists();
                TableRegistry::get('Artists')->saveBatch($artists);
                $taskTable->touch("popular_artists");
                $this->out("\t\t<info>Completed</info>");
                return;
            } else {
                $this->out("\tWe did not receive a list of popular artists.");
            }

        } else {
            $this->out("\tPopular artists already seem up to date.");
        } */

        $this->out("\t<info>Completed.</info>");
    }
}



/*
class PopulateArtistDiscographyTask extends Shell {

    public $uses = array('Artist', 'Album');

    public function execute()
    {
        $expiredIds = $this->Artist->query("
            SELECT
                Artist.id
            FROM artists as Artist
            LEFT JOIN lastfm_artists as Lastfm_Artist on Artist.id = Lastfm_Artist.artist_id
            WHERE
                Artist.id NOT IN (SELECT artist_id FROM albums)
                AND Lastfm_Artist.lastsync < " .  $this->Artist->LastfmArtist->getExpiredRange() . "
            LIMIT 200;
        ");


        $this->out(sprintf("Found <comment>%s artist discographies</comment> that are out of sync.", count($expiredIds)));

        if(count($expiredIds))
        {
            $expiredArtists = $this->Artist->findAllById(Hash::extract($expiredIds, "{n}.Artist.id"));
            foreach ($expiredArtists as $artist)
            {
                $this->Album->data = $artist;
                $this->out(sprintf("\t<info>%d\t%s</info>", $artist["Artist"]["id"], $artist["Artist"]["name"]));
                $this->Album->updateDiscography($artist["Artist"]["name"]);
            }
        }
    }
}
*/

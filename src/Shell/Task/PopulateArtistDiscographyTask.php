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
        $task = $taskTable->getByName('artists_discographies');

        if ($task->requiresUpdate()) {

            $artistsTbl = TableRegistry::get('Artists');
            $lsatfmAlbumsTbl = TableRegistry::get('LastfmAlbums');
            $expiredArtists = $artistsTbl->getWithExpiredDiscographies($task->getTimeout())->all();
            $lastfmApi = new LastfmApi();

            if (count($expiredArtists)) {
                $this->out(sprintf("\tFound <comment>%s artists</comment> that are out of sync.", count($expiredArtists)));

                foreach ($expiredArtists as $artist) {
                    $this->out(sprintf("\t\t%d<info>\t%s</info>...", $artist->id, $artist->name));
                    $artist->fetchDiscography();
                    $taskTable->touch('artists_discographies');
                }


            } else {
                $this->out("\tArtist discographies are up-to-date.");
            }
        } else {
            $this->out("\tArtist discographies update is not ready to run.");
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

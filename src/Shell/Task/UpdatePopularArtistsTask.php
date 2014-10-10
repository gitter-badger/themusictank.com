<?php

namespace App\Shell\Task;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Artist;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class UpdatePopularArtistsTask extends Shell {

    public function execute()
    {

        $this->out("Updating <comment>popular artists</comment>...");

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
                TableRegistry::get('Artists')->saveLastFmBatch($artists);
                $taskTable->touch("popular_artists");

            } else {
                $this->out("\tWe did not receive a list of popular artists.");
            }

        } else {
            $this->out("\tPopular artists do not require an update.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

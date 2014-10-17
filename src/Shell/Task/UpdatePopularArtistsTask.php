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

            $taskTable->touch("popular_artists");
            $lastfmApi = new LastfmApi();
            $topArtistsData = $lastfmApi->getTopArtists();

            if (count($topArtistsData)) {
                $artistList = TableRegistry::get('Artists')->saveLastFmBatch($topArtistsData, true);
                TableRegistry::get('LastfmArtists')->demotePopularArtists();
                TableRegistry::get('LastfmArtists')->promotePopularArtists($artistList);
            } else {
                $this->out("\tWe did not receive a list of popular artists.");
            }

        } else {
            $this->out("\tPopular artists do not require an update.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

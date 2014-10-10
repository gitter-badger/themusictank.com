<?php

namespace App\Shell\Task;

use App\Model\Api\LastfmApi;
use App\Model\Entity\Artist;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateArtistDetailsTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>artist details</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('artists_details');

        if ($task->requiresUpdate()) {

            $artistsTbl = TableRegistry::get('Artists');
            $lastfmArtistTbl = TableRegistry::get('LastfmArtists');
            $lastfmApi = new LastfmApi();
            $expiredArtists = $artistsTbl->getWithExpiredDetails($task->getTimeout())->all();

            if (count($expiredArtists)) {
                $this->out(sprintf("\tFound <comment>%s artist</comment> that are out of sync.", count($expiredArtists)));

                foreach ($expiredArtists as $artist) {
                    $this->out(sprintf("\t\t%d<info>\t%s</info>...", $artist->id, $artist->name));
                    $artist->loadFromLastFm($lastfmApi->getArtistInfo($artist));
                    $artistsTbl->save($artist);

                    // Not entirely sure why the previous save statement
                    // doesn't save the data. Explicitely save lastfm
                    $artist->lastfm->modified = new \DateTime();
                    $lastfmArtistTbl->save($artist->lastfm);
                    $taskTable->touch('artists_discographies');
                }

            } else {
                $this->out("\tArtist details are up-to-date.");
            }
        } else {
            $this->out("\tArtist details update is not ready to run.");
        }


        $taskTable->touch('artists_details');
        $this->out("\t<info>Completed.</info>");
    }
}

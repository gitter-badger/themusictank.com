<?php

namespace App\Shell\Task;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateArtistDiscographyTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>artist discographies</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('artists_discographies');

        if ($task->requiresUpdate()) {

            //$taskTable->touch('artists_discographies');

            $tblLastFmAlbums = TableRegistry::get('LastfmAlbums');
            $tblLastFmAlbums->find('expired', ['timeout' => $task->getTimeout()])->toArray();
            $missing = TableRegistry::get('Artists')->find()->where(['id NOT IN ' => $tblLastFmAlbums->find('listArtistIds')])->toArray();
            $count = count($expired) + count($missing);

            if ($count) {
                $this->out(sprintf("\tFound <comment>%s artists</comment> that are out of sync.", count($expired)));
                $this->out(sprintf("\tFound <comment>%s artists</comment> that are missing a discography.", count($missing)));

                foreach (array_merge($expired, $missing) as $idx => $artist) {
                    $this->out(sprintf("\t\t%d/%d\t%d <info>%s</info>...", $idx+1, $count, $artist->id, $artist->name));
                    TableRegistry::get('Albums')->find('updatedDiscography', ['artist' => $artist]);
                }

            } else {
                $this->out("\tArtist discographies are up-to-date.");
            }
        } else {
            $this->out("\tArtist discographies update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

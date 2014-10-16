<?php

namespace App\Shell\Task;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateArtistDetailsTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>artist details</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('artists_details');

        if ($task->requiresUpdate()) {

            $tblArtists = TableRegistry::get('Artists');
            $expiredArtists = $tblArtists->find('expired')->all();

            if (count($expiredArtists)) {
                $this->out(sprintf("\tFound <comment>%s artist</comment> that are out of sync.", count($expiredArtists)));

                foreach ($expiredArtists as $idx => $artist) {
                    $this->out(sprintf("\t\t%d/%d\t%d <info>%s</info>...", $idx+1, count($expiredArtists), $artist->id, $artist->name));
                    $tblArtists->syncToRemote($artist);
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

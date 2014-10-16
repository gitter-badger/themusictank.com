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

            $albumsTbl = TableRegistry::get('Albums');
            $expiredArtists = $albumsTbl->getExpired($task->getTimeout())->all();

            if (count($expiredArtists)) {
                $this->out(sprintf("\tFound <comment>%s artists</comment> that are out of sync.", count($expiredArtists)));

                foreach ($expiredArtists as $idx => $artist) {
                    $this->out(sprintf("\t\t%d/%d\t%d <info>%s</info>...", $idx+1, count($expiredArtists), $artist->id, $artist->name));
                    $artist->fetchDiscography();
                }
                $taskTable->touch('artists_discographies');

            } else {
                $this->out("\tArtist discographies are up-to-date.");
            }
        } else {
            $this->out("\tArtist discographies update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

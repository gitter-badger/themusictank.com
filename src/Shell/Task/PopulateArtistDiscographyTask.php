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

            $expired = TableRegistry::get('Artists')->find('expiredDiscographies', ['timeout' => $task->getTimeout()])->toArray();
            $missing = TableRegistry::get('Artists')->find('missingDiscographies')->toArray();
            $count = count($expired) + count($missing);

            if ($count) {
                $this->out(sprintf("\tFound <comment>%s artists</comment> that are out of sync.", count($expired)));
                $this->out(sprintf("\t\tFound <comment>%s artists</comment> that need to be looked up for new albums.", count($expired)));
                foreach ($expired as $idx => $artist) {
                    $this->out(sprintf("\t\t  %d/%d\t%d <info>%s</info>...", $idx+1, count($expired), $artist->id, $artist->name));
                    TableRegistry::get('Albums')->find('updatedDiscography', ['artist' => $artist, 'shell' => $this]);
                }

                $this->out(sprintf("\t\tFound <comment>%s artists</comment> that are missing a discography.", count($missing)));
                foreach ($missing as $idx => $artist) {
                    $this->out(sprintf("\t\t  %d/%d\t%d <info>%s</info>...", $idx+1, count($missing), $artist->id, $artist->name));
                    TableRegistry::get('Albums')->find('updatedDiscography', ['artist' => $artist, 'shell' => $this]);
                }

            } else {
                $this->out("\tArtist discographies are up-to-date.");
            }

            $taskTable->touch('artists_discographies');

        } else {
            $this->out("\tArtist discographies update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

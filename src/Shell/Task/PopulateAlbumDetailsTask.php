<?php

namespace App\Shell\Task;


use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class PopulateAlbumDetailsTask extends Shell {

    public function execute()
    {
        $this->out("Updating <comment>album details</comment>...");

        $taskTable = TableRegistry::get('Tasks');
        $task = $taskTable->getByName('album_details');

        if ($task->requiresUpdate()) {

            $taskTable->touch('album_details');
            $albumsTbl = TableRegistry::get('Albums');
            $expiredAlbums = $albumsTbl->getExpired($task->getTimeout())->all();

            if (count($expiredAlbums)) {
                $this->out(sprintf("\tFound <comment>%s albums</comment> that are out of sync.", count($expiredAlbums)));

                foreach ($expiredAlbums as $idx => $album) {
                    $this->out(sprintf("\t\t%d/%d\t%d <info>\t%s</info>...", $idx+1, count($expiredAlbums), $album->id, $album->name));
                    $album->syncToRemote();
                }

            } else {
                $this->out("\tAlbum details are up-to-date.");
            }
        } else {
            $this->out("\tAlbum details update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}

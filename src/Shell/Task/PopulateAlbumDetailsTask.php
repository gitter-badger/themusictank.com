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

            $albumsTbl = TableRegistry::get('Albums');
            $expiredAlbums = $albumsTbl->getExpired($task->getTimeout())->all();

            if (count($expiredAlbums)) {
                $this->out(sprintf("\tFound <comment>%s albums</comment> that are out of sync.", count($expiredAlbums)));

                foreach ($expiredAlbums as $album) {
                    $this->out(sprintf("\t\t%d<info>\t%s</info>...", $album->id, $album->name));
                    $album->syncToRemote();
                }
                $taskTable->touch('album_details');

            } else {
                $this->out("\Album details are up-to-date.");
            }
        } else {
            $this->out("\tAlbum details update is not ready to run.");
        }

        $this->out("\t<info>Completed.</info>");
    }
}


/*
class PopulateAlbumDetailsTask extends Shell {

    public $uses = array('LastfmAlbum');

    public function execute()
    {
        // Start with expired albums
        $expired = $this->LastfmAlbum->Album->find("all", array(
            "conditions" => array(
                "or" => array(
                    "LastfmAlbum.lastsync IS NULL",
                    "LastfmAlbum.lastsync < " . $this->LastfmAlbum->getExpiredRange()
                )
            ),
            "fields"    => array("Album.*", "Artist.*", "LastfmAlbum.*"),
            "limit" => 200 // I think it's better to do a few of them at the time.
        ));

        $this->out(sprintf("Found <comment>%s albums</comment> that are out of sync.", count($expired)));
        foreach ($expired as $album) {
            $this->LastfmAlbum->data = $album;
            $this->out(sprintf("\t<info>%d\t%s</info>", $this->LastfmAlbum->getData("Album.id"), $this->LastfmAlbum->getData("Album.name")));
            $this->LastfmAlbum->updateCached();
        }
    }
}
*/

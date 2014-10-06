<?php
namespace App\Shell\Task;

use App\Model\Entity\AlbumReviewSnapshot;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class AlbumSnapshotsSyncTask extends Shell {

    public function execute()
    {
        $albumIdsToSync = array();
        $tblAlbumReviewSnapshots = TableRegistry::get('album_review_snapshots');

        $this->out("Syncing <comment>album</comment> review snapshots...");

        // Check whether the new reviews have been taken into account
        $albumIdsToSync = array_merge($albumIdsToSync, $tblAlbumReviewSnapshots->getIdsWithNoSnapshots());

        // Check whether a default snapshot was not created for a new album
        $albumIdsToSync = array_merge($albumIdsToSync, $tblAlbumReviewSnapshots->getMissingIds());

        // Now update the expired snapshots
        $albumIdsToSync = array_merge($albumIdsToSync, $tblAlbumReviewSnapshots->getExpiredIds());

        if (count($albumIdsToSync)) {

            $expired = TableRegistry::get('albums')->find()
                ->select(['id','name'])
                ->where(['id IN' => $albumIdsToSync])->all();

            $this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
            foreach ($expired as $album) {

                $snapshot = new AlbumReviewSnapshot();
                $snapshot->album_id = $album->id;
                $snapshot->fetch();
                $tblAlbumReviewSnapshots->save($snapshot);

                $this->out(sprintf("\t<info>%d\t%s</info>", $album->id, $album->name));
            }
        }

        $this->out("\t<info>Completed</info>");
    }
}

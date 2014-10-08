<?php

namespace App\Shell\Task;

use App\Model\Entity\albumReviewSnapshot;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class AlbumSnapshotsSyncTask extends Shell {

    public function createNewSnapshots()
    {
        $tblalbumReviewSnapshots = TableRegistry::get('album_review_snapshots');

        // Check whether the new reviews have been taken into account
        $expired = TableRegistry::get('albums')->find()
            ->select(['id','name'])
            ->where(['id IN' => $tblalbumReviewSnapshots->getIdsWithNoSnapshots()])->all();

        $this->out(sprintf("\t   <info>Found %s new snapshots.</info>", count($expired)));

        foreach ($expired as $album) {
            $snapshot = new albumReviewSnapshot();
            $snapshot->album_id = $album->id;
            $snapshot->fetch();
            $tblalbumReviewSnapshots->save($snapshot);

            $this->out(sprintf("\t\t%d<info>\t%s</info>", $album->id, $album->name));
        }
    }

    public function updateExpiredSnapshots()
    {
        $tblalbumReviewSnapshots = TableRegistry::get('album_review_snapshots');

        // Now update the expired snapshots
        $albumIdsToSync = $tblalbumReviewSnapshots->getExpiredIds();

        $this->out(sprintf("\t   <info>Found %s out of sync snapshots.</info>", count($albumIdsToSync)));

        if (count($albumIdsToSync)) {

            $expired = $tblalbumReviewSnapshots->find()
                ->select(['id', 'album.name', 'album_id'])
                ->contain(['albums'])
                ->where(['album_id IN' => $albumIdsToSync])->all();

            foreach ($expired as $expiredSnapshot) {

                $snapshot = new albumReviewSnapshot();
                $snapshot->id = $expiredSnapshot->id;
                $snapshot->album_id = $expiredSnapshot->album_id;
                $snapshot->fetch();
                $tblalbumReviewSnapshots->save($snapshot);

                $this->out(sprintf("\t\t%d<info>\t%s</info>", $expiredSnapshot->album_id, $expiredSnapshot->album->name));
            }
        }
    }

    public function execute()
    {
        $this->out("\nSyncing <comment>album</comment> review snapshots...");

        $this->out("\tSyncing new snapshots...");
        $this->createNewSnapshots();

        $this->out("\tSyncing out-dated review snapshots...");
        $this->updateExpiredSnapshots();

        $this->out("\tCompleted");
    }
}

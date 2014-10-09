<?php

namespace App\Shell\Task;

use App\Model\Entity\ArtistReviewSnapshot;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class ArtistSnapshotsSyncTask extends Shell {

    public function createNewSnapshots()
    {
        $tblArtistReviewSnapshots = TableRegistry::get('artist_review_snapshots');

        // These are all new records, create them.
        $expired = TableRegistry::get('artists')->find()
            ->select(['id','name'])
            ->where(['id IN' => $tblArtistReviewSnapshots->getIdsWithNoSnapshots()])->all();

        $this->out(sprintf("\t   <info>Found %s new snapshots.</info>", count($expired)));

        foreach ($expired as $artist) {
            $snapshot = new ArtistReviewSnapshot();
            $snapshot->artist_id = $artist->id;
            $snapshot->fetch();
            $tblArtistReviewSnapshots->save($snapshot);

            $this->out(sprintf("\t\t%d<info>\t%s</info>", $artist->id, $artist->name));
        }
    }

    public function updateExpiredSnapshots()
    {
        $tblArtistReviewSnapshots = TableRegistry::get('artist_review_snapshots');

        // Now update the expired snapshots
        $artistIdsToSync = $tblArtistReviewSnapshots->getExpiredIds();

        $this->out(sprintf("\t   <info>Found %s out of sync snapshots.</info>", count($artistIdsToSync)));

        if (count($artistIdsToSync)) {

            $expired = $tblArtistReviewSnapshots->find()
                ->select(['id','Artists.name', 'artist_id'])
                ->contain(['artists'])
                ->where(['artist_id IN' => $artistIdsToSync])->all();

            foreach ($expired as $expiredSnapshot) {

                $snapshot = new ArtistReviewSnapshot();
                $snapshot->id = $expiredSnapshot->id;
                $snapshot->artist_id = $expiredSnapshot->artist_id;
                $snapshot->fetch();
                $tblArtistReviewSnapshots->save($snapshot);

                $this->out(sprintf("\t\t<warning>%d</warning><info>\t%s</info>", $expiredSnapshot->artist_id, $expiredSnapshot->artist["name"]));
            }
        }
    }

    public function execute()
    {
        $this->out("\nSyncing <comment>artist</comment> review snapshots...");

        $this->out("\tSyncing new snapshots...");
        $this->createNewSnapshots();

        $this->out("\tSyncing out-dated review snapshots...");
        $this->updateExpiredSnapshots();

        $this->out("\tCompleted");
    }
}

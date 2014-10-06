<?php

namespace App\Shell\Task;

use App\Model\Entity\ArtistReviewSnapshot;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class ArtistSnapshotsSyncTask extends Shell {

    public function execute()
    {
        $artistIdsToSync = array();
        $tblArtistReviewSnapshots = TableRegistry::get('artist_review_snapshots');

        $this->out("Syncing <comment>artist</comment> review snapshots...");

        // Check whether the new reviews have been taken into account
        $artistIdsToSync = array_merge($artistIdsToSync, $tblArtistReviewSnapshots->getIdsWithNoSnapshots());

        // Check whether a default snapshot was not created for a new artist
        $artistIdsToSync = array_merge($artistIdsToSync, $tblArtistReviewSnapshots->getMissingIds());

        // Now update the expired snapshots
        $artistIdsToSync = array_merge($artistIdsToSync, $tblArtistReviewSnapshots->getExpiredIds());

        if (count($artistIdsToSync)) {

            $expired = TableRegistry::get('artists')->find()
                ->select(['id','name'])
                ->where(['id IN' => $artistIdsToSync])->all();

            $this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
            foreach ($expired as $artist) {

                $snapshot = new ArtistReviewSnapshot();
                $snapshot->artist_id = $artist->id;
                $snapshot->fetch();
                $tblArtistReviewSnapshots->save($snapshot);

                $this->out(sprintf("\t<info>%d\t%s</info>", $artist->id, $artist->name));
            }
        }

        $this->out("\t<info>Completed</info>");
    }
}

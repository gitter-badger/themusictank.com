<?php

namespace App\Shell\Task;

use App\Model\Entity\TrackReviewSnapshot;

use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class TrackSnapshotsSyncTask extends Shell {


    public function createNewSnapshots()
    {
        $tbltrackReviewSnapshots = TableRegistry::get('track_review_snapshots');

        // These are all new records, create them.
        $expired = TableRegistry::get('tracks')->find()
            ->select(['id','title'])
            ->where(['id IN' => $tbltrackReviewSnapshots->getIdsWithNoSnapshots()])->all();

        $this->out(sprintf("\t   <info>Found %s new snapshots.</info>", count($expired)));

        foreach ($expired as $track) {
            $snapshot = new TrackReviewSnapshot();
            $snapshot->track_id = $track->id;
            $snapshot->fetch();
            $tbltrackReviewSnapshots->save($snapshot);

            $this->out(sprintf("\t\t%d<info>\t%s</info>", $track->id, $track->title));
        }
    }

    public function updateExpiredSnapshots()
    {
        $tbltrackReviewSnapshots = TableRegistry::get('track_review_snapshots');

        // Now update the expired snapshots
        $trackIdsToSync = $tbltrackReviewSnapshots->getExpiredIds();

        $this->out(sprintf("\t   <info>Found %s out of sync snapshots.</info>", count($trackIdsToSync)));

        if (count($trackIdsToSync)) {

            $expired = $tbltrackReviewSnapshots->find()
                ->select(['id','Tracks.title', 'track_id'])
                ->contain(['tracks'])
                ->where(['track_id IN' => $trackIdsToSync])
                ->limit(1000)
                ->all();

            foreach ($expired as $expiredSnapshot) {
                $snapshot = new TrackReviewSnapshot();
                $snapshot->id = $expiredSnapshot->id;
                $snapshot->track_id = $expiredSnapshot->track_id;
                $snapshot->fetch();
                $tbltrackReviewSnapshots->save($snapshot);

                $this->out(sprintf("\t\t<warning>%d</warning><info>\t%s</info>", $expiredSnapshot->track_id, $expiredSnapshot->Track["title"]));
            }
        }
    }

    public function execute()
    {
        $this->out("\nSyncing <comment>track</comment> review snapshots...");

        $this->out("\tSyncing new snapshots...");
        $this->createNewSnapshots();

        $this->out("\tSyncing out-dated review snapshots...");
        $this->updateExpiredSnapshots();

        $this->out("\tCompleted");
    }
}

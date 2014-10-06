<?php
namespace App\Shell\Task;

use App\Model\Entity\TrackReviewSnapshot;
use Cake\ORM\TableRegistry;
use Cake\Console\Shell;

class TrackSnapshotsSyncTask extends Shell {

    public function execute()
    {
        $trackIdsToSync = array();
        $tblTrackReviewSnapshot = TableRegistry::get('track_review_snapshots');

        $this->out("Syncing <comment>track</comment> review snapshots...");

        // Check whether the new reviews have been taken into account
        $trackIdsToSync = array_merge($trackIdsToSync, $tblTrackReviewSnapshot->getIdsWithNoSnapshots());

        // Check whether a default snapshot was not created for a new artist
        $trackIdsToSync = array_merge($trackIdsToSync, $tblTrackReviewSnapshot->getMissingIds());

        // Now update the expired snapshots
        $trackIdsToSync = array_merge($trackIdsToSync, $tblTrackReviewSnapshot->getExpiredIds());

        if (count($trackIdsToSync)) {

            $expired = TableRegistry::get('track')->find()
                ->select(['id', 'title'])
                ->where(['id IN' => $trackIdsToSync])->all();

            $this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
            foreach ($expired as $track) {

                $snapshot = new TrackReviewSnapshot();
                $snapshot->track_id = $track->id;
                $snapshot->fetch();
                $tblTrackReviewSnapshot->save($snapshot);

                $this->out(sprintf("\t<info>%d\t%s</info>", $track->id, $track->title));
            }
        }

        $this->out("\t<info>Completed</info>");
    }
}

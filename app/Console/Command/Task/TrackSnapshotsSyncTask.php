<?php
class TrackSnapshotsSyncTask extends Shell {

	public $uses = array('LastfmTrack', 'TrackReviewSnapshot');

    public function execute()
    {
    	$trackIdsToSync = array();

		$this->out("Syncing track review snapshots");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->TrackReviewSnapshot->query("SELECT distinct track_id FROM review_frames where track_id NOT IN (SELECT track_id FROM track_review_snapshots);");
    	if($newIds) {
			$trackIdsToSync = array_merge($trackIdsToSync, Hash::extract($newIds, "{n}.review_frames.track_id"));
    	}

    	// Check whether a default snapshot was not created for a new tracks
		$newTracks = $this->TrackReviewSnapshot->query("SELECT id FROM tracks where id NOT IN (SELECT track_id FROM track_review_snapshots);");
    	if($newTracks) {
			$trackIdsToSync = array_merge($trackIdsToSync, Hash::extract($newTracks, "{n}.tracks.id"));
    	}

    	$expiredIds = $this->TrackReviewSnapshot->find("list", array(
    		'fields' => array('TrackReviewSnapshot.track_id'),
    		"conditions" => array(
    			"or" => array(
    				"TrackReviewSnapshot.lastsync IS NULL",
    				"TrackReviewSnapshot.lastsync < " . $this->TrackReviewSnapshot->getExpiredRange()
				)
			)
		));
    	if($expiredIds) {
			$trackIdsToSync = array_merge($trackIdsToSync, Hash::extract($expiredIds, "{n}.TrackReviewSnapshot.track_id"));
		}

    	if(count($trackIdsToSync))
    	{
	 		$expired = $this->LastfmTrack->Track->find("all", array(
	    		"conditions" => array("Track.id" => $trackIdsToSync),
	            "fields"    => array("Track.*", "LastfmTrack.*")
			));

    		$this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
			foreach ($expired as $track) {
	    		$this->TrackReviewSnapshot->data = $track;
	    		$this->out(sprintf("Syncing %s (%d)", $this->TrackReviewSnapshot->getData("Track.title"), $this->TrackReviewSnapshot->getData("Track.id")));
	    		$this->TrackReviewSnapshot->fetch($this->TrackReviewSnapshot->getData("Track.id"));
	    	}
		}
    }
}

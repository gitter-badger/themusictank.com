<?php

class SyncTracksShell extends AppShell {

	public $uses = array('LastfmTrack', 'TrackReviewSnapshot');

	// /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php SyncTracks syncLastFm themusictank.nvi
	public function syncLastFm() {
		$expired = $this->LastfmTrack->Track->find("all", array(
			"conditions" => array(
				"or" => array(
					"LastfmTrack.lastsync IS NULL",
					"LastfmTrack.lastsync < " . $this->LastfmTrack->getExpiredRange()
				)
			),
            "contain" 		=> array("LastfmTrack", "Album" => array("Artist")),
            "limit"			=> 200
        ));

		$this->out(sprintf("Found %s tracks that are out of sync.", count($expired)));
		foreach ($expired as $track) {
			$this->LastfmTrack->data = $track;
			$this->LastfmTrack->data["Album"] = $track["Album"];
			$this->LastfmTrack->data["Artist"] = $track["Album"]["Artist"];

			$this->out(sprintf("Syncing %s", $this->LastfmTrack->getData("Track.title")));
			$this->LastfmTrack->updateCached();
		}
	}

    public function syncSnapshots()
    {
    	$trackIdsToSync = array();

		$this->out("Syncing track review snapshots");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->TrackReviewSnapshot->query("SELECT distinct track_id FROM review_frames where track_id NOT IN (SELECT track_id FROM track_review_snapshots);");
    	if($newIds) {
			$trackIdsToSync = array_merge($trackIdsToSync, Hash::extract($newIds, "{n}.review_frames.track_id"));
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
	            "fields"    => array("Track.*", "LastfmTrack.*"),
				"limit" => 200 // I think it's better to do a few of them at the time.
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

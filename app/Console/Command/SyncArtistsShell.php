<?php

class SyncArtistsShell extends AppShell {

    public $uses = array('LastfmArtist', 'ArtistReviewSnapshot');

	// /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php SyncArtists syncLastFm themusictank.nvi
    public function syncLastFm() {
    	$expired = $this->LastfmArtist->find("all", array(
    		"conditions" => array(
    			"or" => array(
    				"lastsync IS NULL",
    				"lastsync < " . $this->LastfmArtist->getExpiredRange()
				)
			),
			"limit" => 200 // I think it's better to do a few of them at the time.
		));

    	$this->out(sprintf("Found %s artist that are out of sync.", count($expired)));
    	foreach ($expired as $artist) {
    		$this->LastfmArtist->data = $artist;
    		$this->out(sprintf("Syncing %s", $this->LastfmArtist->getData("Artist.name")));
    		$this->LastfmArtist->updateCached();
    	}
    }

    public function syncSnapshots()
    {
    	$artistIdsToSync = array();

		$this->out("Syncing artist review snapshots");
		$this->out("-------------------------------");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->ArtistReviewSnapshot->query("SELECT distinct artist_id FROM review_frames where artist_id NOT IN (SELECT artist_id FROM artist_review_snapshots);");
    	if($newIds) {
			$artistIdsToSync = array_merge($artistIdsToSync, Hash::extract($newIds, "{n}.review_frames.artist_id"));
    	}


    	$expiredIds = $this->ArtistReviewSnapshot->find("list", array(
    		'fields' => array('ArtistReviewSnapshot.artist_id'),
    		"conditions" => array(
    			"or" => array(
    				"ArtistReviewSnapshot.lastsync IS NULL",
    				"ArtistReviewSnapshot.lastsync < " . $this->ArtistReviewSnapshot->getExpiredRange()
				)
			)
		));
    	if($expiredIds) {
			$artistIdsToSync = array_merge($artistIdsToSync, Hash::extract($expiredIds, "{n}.ArtistReviewSnapshot.artist_id"));
		}

    	if(count($artistIdsToSync))
    	{
	 		$expired = $this->LastfmArtist->Artist->find("all", array(
	    		"conditions" => array("Artist.id" => $artistIdsToSync),
	            "fields"    => array("Artist.*", "LastfmArtist.*"),
				"limit" => 200 // I think it's better to do a few of them at the time.
			));

    		$this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
			foreach ($expired as $artist) {
	    		$this->ArtistReviewSnapshot->data = $artist;
	    		$this->out(sprintf("Syncing %s (%d)", $this->ArtistReviewSnapshot->getData("Artist.name"), $this->ArtistReviewSnapshot->getData("Artist.id")));
	    		$this->ArtistReviewSnapshot->fetch($this->ArtistReviewSnapshot->getData("Artist.id"));
	    	}
		}
    }
}

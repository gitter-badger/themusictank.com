<?php
class ArtistSnapshotsSyncTask extends Shell {

    public $uses = array('LastfmArtist', 'ArtistReviewSnapshot');

    public function execute()
    {
    	$artistIdsToSync = array();

		$this->out("Syncing <comment>artist</comment> review snapshots...");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->ArtistReviewSnapshot->query("SELECT distinct artist_id FROM review_frames where artist_id NOT IN (SELECT artist_id FROM artist_review_snapshots);");
    	if($newIds) {
			$artistIdsToSync = array_merge($artistIdsToSync, Hash::extract($newIds, "{n}.review_frames.artist_id"));
    	}

    	// Check whether a default snapshot was not created for a new artist
		$newArtists = $this->ArtistReviewSnapshot->query("SELECT id FROM artists where id NOT IN (SELECT artist_id FROM artist_review_snapshots);");
    	if($newArtists) {
			$artistIdsToSync = array_merge($artistIdsToSync, Hash::extract($newArtists, "{n}.artists.id"));
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
	            "fields"    => array("Artist.*", "LastfmArtist.*")
			));

    		$this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
			foreach ($expired as $artist) {
	    		$this->ArtistReviewSnapshot->data = $artist;
	    		$this->out(sprintf("\t<info>%d\t%s</info>", $this->ArtistReviewSnapshot->getData("Artist.id"), $this->ArtistReviewSnapshot->getData("Artist.name")));
	    		$this->ArtistReviewSnapshot->fetch($this->ArtistReviewSnapshot->getData("Artist.id"));
	    	}
		}


		$this->out("\t<info>Completed</info>");
    }
}

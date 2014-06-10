<?php

class SyncAlbumsShell extends AppShell {

    public $uses = array('LastfmAlbum', 'AlbumReviewSnapshot');

	// /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php SyncAlbums syncLastFm themusictank.nvi
    public function syncLastFm() {
    	$expired = $this->LastfmAlbum->Album->find("all", array(
    		"conditions" => array(
    			"or" => array(
    				"LastfmAlbum.lastsync IS NULL",
    				"LastfmAlbum.lastsync < " . $this->LastfmAlbum->getExpiredRange()
				)
			),
            "fields"    => array("Album.*", "Artist.*", "LastfmAlbum.*"),
			"limit" => 200 // I think it's better to do a few of them at the time.
		));

    	$this->out(sprintf("Found %s albums that are out of sync.", count($expired)));
    	foreach ($expired as $album) {
    		$this->LastfmAlbum->data = $album;
    		$this->out(sprintf("Syncing %s (%d)", $this->LastfmAlbum->getData("Album.name"), $this->LastfmAlbum->getData("Album.id")));
    		$this->LastfmAlbum->updateCached();
    	}
    }

    public function syncSnapshots()
    {
    	$albumsIdsToSync = array();

		$this->out("Syncing album review snapshots");
		$this->out("------------------------------");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->AlbumReviewSnapshot->query("SELECT distinct album_id FROM review_frames where album_id NOT IN (SELECT album_id FROM album_review_snapshots);");
    	if($newIds) {
			$albumsIdsToSync = array_merge($albumsIdsToSync, Hash::extract($newIds, "{n}.review_frames.album_id"));
    	}

    	$expiredIds = $this->AlbumReviewSnapshot->find("list", array(
    		'fields' => array('AlbumReviewSnapshot.album_id'),
    		"conditions" => array(
    			"or" => array(
    				"AlbumReviewSnapshot.lastsync IS NULL",
    				"AlbumReviewSnapshot.lastsync < " . $this->AlbumReviewSnapshot->getExpiredRange()
				)
			)
		));
    	if($expiredIds) {
			$albumsIdsToSync = array_merge($albumsIdsToSync, Hash::extract($expiredIds, "{n}.AlbumReviewSnapshot.album_id"));
		}

    	if(count($albumsIdsToSync))
    	{
	 		$expired = $this->LastfmAlbum->Album->find("all", array(
	    		"conditions" => array("Album.id" => $albumsIdsToSync),
	            "fields"    => array("Album.*", "Artist.*", "LastfmAlbum.*"),
				"limit" => 200 // I think it's better to do a few of them at the time.
			));

    		$this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
			foreach ($expired as $album) {
	    		$this->AlbumReviewSnapshot->data = $album;
	    		$this->out(sprintf("Syncing %s (%d)", $this->AlbumReviewSnapshot->getData("Album.name"), $this->AlbumReviewSnapshot->getData("Album.id")));
	    		$this->AlbumReviewSnapshot->fetch($this->AlbumReviewSnapshot->getData("Album.id"));
	    	}
		}
    }
}

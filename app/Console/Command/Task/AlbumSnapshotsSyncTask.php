<?php
class AlbumSnapshotsSyncTask extends Shell {

    public $uses = array('AlbumReviewSnapshot', 'LastfmAlbum');

    public function execute()
    {
		$albumsIdsToSync = array();

		$this->out("Syncing album review snapshots...");

    	// Check whether the new reviews have been taken into account
    	$newIds = $this->AlbumReviewSnapshot->query("SELECT distinct album_id FROM review_frames where album_id NOT IN (SELECT album_id FROM album_review_snapshots);");
    	if($newIds) {
			$albumsIdsToSync = array_merge($albumsIdsToSync, Hash::extract($newIds, "{n}.review_frames.album_id"));
    	}

    	// Check whether a default snapshot was not created for a new album
		$newAlbums = $this->AlbumReviewSnapshot->query("SELECT id FROM albums where id NOT IN (SELECT album_id FROM album_review_snapshots);");
    	if($newAlbums) {
			$albumsIdsToSync = array_merge($albumsIdsToSync, Hash::extract($newAlbums, "{n}.albums.id"));
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
	            "fields"    => array("Album.*", "Artist.*", "LastfmAlbum.*")
			));

    		$this->out(sprintf("Found %s snapshots that are out of sync or new.", count($expired)));
			foreach ($expired as $album) {
	    		$this->AlbumReviewSnapshot->data = $album;
	    		$this->out(sprintf("Syncing %s (%d)", $this->AlbumReviewSnapshot->getData("Album.name"), $this->AlbumReviewSnapshot->getData("Album.id")));
	    		$this->AlbumReviewSnapshot->fetch($this->AlbumReviewSnapshot->getData("Album.id"));
	    	}
		}

		$this->out("Completed syncing album review snapshots.");
    }
}

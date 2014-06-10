<?php

App::uses('TableSnapshot', 'Model');
class AlbumReviewSnapshot extends TableSnapshot
{
	public $name        = 'AlbumReviewSnapshot';
    public $useTable    = 'album_review_snapshots';
    public $belongsTo   = array('Album');
    public $actsAs      = array('Metacritic');

    public function fetch($albumId)
    {
    	$this->data["Album"] = array("id" => $albumId);

		$existing = $this->findByAlbumId($albumId);
		if ($existing) {
			$this->data["AlbumReviewSnapshot"] = Hash::extract($existing, "AlbumReviewSnapshot");
		}

        $result = $this->updateCached(array("ReviewFrames.album_id" => $albumId));
        return ($result)  ? $result : array();
    }
}

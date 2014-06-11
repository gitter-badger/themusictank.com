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
		$existing = $this->findByAlbumId($albumId);
		if ($existing) {
			$this->data[$this->alias] = Hash::extract($existing, $this->alias);
		}

        $result = $this->updateCached(array("ReviewFrames.album_id" => $albumId));
        return ($result)  ? $result : array();
    }

    public function getExtraSaveFields($conditions = array())
    {
    	$extra = parent::getExtraSaveFields($conditions);
		$extra = Hash::insert($extra, "album_id", $conditions["ReviewFrames.album_id"]);
    	return $extra;
    }
}

<?php

App::uses('User', 'Model');
App::uses('UserAlbumReviewSnapshot', 'Model');
App::uses('AlbumReviewSnapshot', 'Model');
App::uses('CakeSession', 'Model/Datasource');
App::uses('OEmbedable', 'Model');

class Album extends OEmbedable
{
	public $hasOne      = array('RdioAlbum', "LastfmAlbum");
    public $hasMany     = array('Tracks' => array('order' => 'track_num ASC'));
    public $belongsTo   = "Artist";

    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }

    public function search($query, $limit = 10)
    {
        return $this->find('all', array(
            "conditions" => array("Album.name LIKE" => sprintf("%%%s%%", $query)),
            "fields"     => array("Album.slug", "Album.name", "Album.image", "Artist.name", "Artist.slug"),
            "recursive"  => 0,
            "limit"      => $limit
        ));
    }

    public function getUpdatedSetBySlug($slug, $addCurrentUser = false)
    {
        $syncValues = $this->find("first", array(
            "conditions" => array("Album.slug" => $slug),
            "fields"    => array("Album.*", "RdioAlbum.*", "Artist.*", "LastfmAlbum.*", "RdioAlbum.*")
        ));

        if(count($syncValues)) {

            $this->RdioAlbum->data = $syncValues;
            $this->RdioAlbum->updateCached();
            $syncValues["RdioAlbum"] = $this->RdioAlbum->data["RdioAlbum"];

            $this->LastfmAlbum->data = $syncValues;
        	$this->LastfmAlbum->updateCached();
            $syncValues["LastfmAlbum"] = $this->LastfmAlbum->data["LastfmAlbum"];

            $this->data = $syncValues;
            return $syncValues;
        }
    }

    /**
     * Saves a list of RdioAlbums.
     * @param type $artistId
     * @param type $rdioKey
     * @param array $albums A dataset of Albums returned by Rdio
     * @return boolean True on success, false on failure
     */
    public function saveDiscography($albums)
    {
        $artistId   = $this->getData("Artist.id");
        $rdioKey    = $this->getData("RdioArtist.key");
        $filtered   = $this->RdioAlbum->filterNew($artistId, $rdioKey, $albums);

        if(count($filtered))
        {
            return $this->saveMany($filtered, array('deep' => true));
        }
        return true;
    }

    public function setNewReleases($newReleasesIds)
    {
        return $this->updateAll(array("Album.is_newrelease" => true), array("Album.id" => $newReleasesIds));
    }

    public function resetArtistNotables($artistId)
    {
        $this->updateAll(array("Album.notability" => 'NULL'), array("Album.artist_id" => $artistId));
    }

    public function resetNewReleases()
    {
        return $this->updateAll(array("Album.is_newrelease" => false), array("Album.is_newrelease" => true));
    }

    public function getNewReleases($limit = null)
    {
        return $this->find("all", array(
            "conditions" => array("is_newrelease" => true),
            "limit" => $limit,
            "order" => array("release_date DESC")
        ));
    }

    public function toOEmbed($additionalData = array())
    {
        $data = $this->getSnapshot();
        unset($data["album_id"]);
        unset($data["id"]);
        unset($data["metacritic_score"]);
        unset($data["snapshot_ppf"]);

        $data = Hash::insert($data, "Album.title", $this->getData("Album.name"));
        $data = Hash::insert($data, "Album.slug", $this->getData("Album.slug"));

        return parent::toOEmbed(array_merge($additionalData, $data));
    }

    public function getSnapshot()
    {
		$reviews = new AlbumReviewSnapshot();
        $reviews->data = array(
            "Album" => array(
                "id" => $this->getData("Album.id"),
                "name" => $this->getData("Album.name")
            ),
            "Artist" => array(
                "name" => $this->getData("Artist.name")
            )
        );
    	return $reviews->fetch($this->getData("Album.id"));
    }

    public function getUserSnapshot($userId)
    {
		$reviews = new UserAlbumReviewSnapshot();
    	return $reviews->fetch($this->getData("Album.id"), $userId);
    }

    public function getUserSubscribersSnapshot($userIds)
    {
		$reviews = new SubscribersAlbumReviewSnapshot();
    	return $reviews->fetch($this->getData("Album.id"), $userIds);
    }


    public function addTracksSnapshots()
    {
        $trackIds       = Hash::extract($this->data, "Tracks.{n}.id");
        $trackSnapshots = $this->Tracks->getSnapshotsByTrackIds($trackIds);

        if($trackSnapshots)
        {
            foreach($trackSnapshots as $i => $snapshot)
            {
                $this->data["Tracks"][$i]["TrackReviewSnapshot"] = $snapshot;
            }
            return $this->data;
        }
        return false;
    }

}

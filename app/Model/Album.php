<?php

App::uses('User', 'Model');
App::uses('UserAlbumReviewSnapshot', 'Model');
App::uses('AlbumReviewSnapshot', 'Model');
App::uses('SubscribersAlbumReviewSnapshot', 'Model');
App::uses('OEmbedable', 'Model');

class Album extends OEmbedable
{
	public $hasOne      = array('LastfmAlbum', "AlbumReviewSnapshot");
    public $hasMany     = array('Tracks' => array('order' => 'track_num ASC'));
    public $belongsTo   = array('Artist');

    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }

    public function getFirstBySlug($slug)
    {
    	$this->data = $this->find("first", array(
            "conditions" => array("Album.slug" => $slug)
        ));
        return $this->data;
    }

    public function getFirstById($id)
    {
    	$this->data = $this->find("first", array(
            "conditions" => array("Album.id" => $id)
        ));
        return $this->data;
    }

    public function search($query, $limit = 10)
    {
        return $this->find('all', array(
            "conditions" => array("Album.name LIKE" => sprintf("%%%s%%", $query)),
            "fields"     => array("Album.slug", "Album.name", "Album.image", "Album.release_date", "Artist.name", "Artist.slug", "AlbumReviewSnapshot.*"),
            "recursive"  => 0,
            "limit"      => $limit,
            "order"		 => array("LOCATE('".$query."', Album.name)", "Album.name")
        ));
    }

    public function updateDiscography()
    {
        // only update when cache is out of date.
        $alreadyLoadedAlbums 	= Hash::get($this->data, "Albums");
        if(count($alreadyLoadedAlbums) > 0)
        {
            if(!$this->LastfmAlbum->requiresUpdate())
            {
                return $alreadyLoadedAlbums;
            }
        }

        $artistName 	= $this->getData("Artist.name");
        $artistId   	= $this->getData("Artist.id");
        $artistMbid   	= $this->getData("LastfmArtist.mbid");
        $existingAlbums = Hash::extract($this->findAllByArtistId($artistId), "{n}.LastfmAlbum.mbid");
        $apiResult 		= $this->LastfmAlbum->getArtistTopAlbums($artistName);
        $albums 		= array();

		if(is_null($apiResult)) {
			return array();
		}

        // Ensure that we are dealing with arrays
        if(is_object($apiResult)) {
        	$apiResult = array($apiResult);
        }

        foreach($apiResult as $album)
        {
    		// Albums which do not have a musicbrainz id are not
    		// considered important enough. If we miss too many
    		// albums, maybe we could remove this. (Had to do it for tracks)
            if(trim($album->mbid) != "")
            {
            	// Ensure the artist is the one we are expecting and that it's a new one.
                if($album->artist->mbid == $artistMbid && !in_array($album->mbid, $existingAlbums))
                {
                    $albums[] = array(
                        "LastfmAlbum" => array(
                            "mbid" => $album->mbid
                        ),
                        "Album" => array(
                            "artist_id" => $artistId,
                            "name" => $album->name,
                            "mbid" => $album->mbid,
                            "slug" => null,
                            "release_date" => 0,
                            "notability" => $album->{"@attr"}->rank,
                            "image"     => empty($album->image[4]->{'#text'}) ? null : $this->getImageFromUrl($album->image[4]->{'#text'}, $this->getData("Album.image")),
                            "image_src" => empty($album->image[4]->{'#text'}) ? null : $album->image[4]->{'#text'}
                        )
                    );
                }
            }
        }

        if(count($albums) > 0)
        {
        	// The albums have been formated and batched. We can validate the slugs
        	// and skip the unique key issues we had when the slug was set in the loop above
        	$slugs = $this->batchSlugs(Hash::extract($albums, "{n}.Album.name"));
        	foreach($slugs as $idx => $slug) {
        		$albums[$idx]["Album"]["slug"] = $slug;
        	}
            return $this->saveMany($albums, array("deep" => true)) ? $albums : false;
        }

        return $alreadyLoadedAlbums;
    }

    public function setNewReleases($newReleasesIds)
    {
        return $this->updateAll(array("Album.is_newrelease" => true), array("Album.id" => $newReleasesIds));
    }

    public function resetArtistNotables($artistId)
    {
        return $this->updateAll(array("Album.notability" => 'NULL'), array("Album.artist_id" => $artistId));
    }

    public function resetNewReleases()
    {
        return $this->updateAll(array("Album.is_newrelease" => false), array("Album.is_newrelease" => true));
    }

    public function getNewReleases($limit = null)
    {
        return $this->find("all", array(
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

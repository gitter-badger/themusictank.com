<?php

App::uses('OEmbedable', 'Model');
App::uses('TrackReviewSnapshot', 'Model');
App::uses('UserTrackReviewSnapshot', 'Model');
App::uses('SubscribersTrackReviewSnapshot', 'Model');

class Track extends OEmbedable
{
	public $hasOne 		= array('LastfmTrack', 'TrackReviewSnapshot');
	public $belongsTo 	= array('Album');
	public $actsAs 		= array('Containable');
	public $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A name is required'
			)
		)
	);

	public function beforeSave($options = array())
	{
		$this->checkSlug(array('title'));

		if(array_key_exists("wavelength", $this->data[$this->alias]) && !is_string($this->data[$this->alias]["wavelength"]))
		{
			$this->data[$this->alias]["wavelength"] = json_encode($this->data[$this->alias]["wavelength"]);
		}

		return true;
	}

	public function afterFind($results, $primary = false)
	{
		foreach($results as $idx => $row)
		{
			if(array_key_exists($this->alias, $row))
			{
				if(array_key_exists("wavelength", $row[$this->alias]) && is_string($row[$this->alias]["wavelength"]))
				{
					$results[$idx][$this->alias]["wavelength"] = json_decode($row[$this->alias]["wavelength"]);
				}
			}
		}
		return $results;
	}

	public function search($query, $limit = 10)
	{
		return $this->find('all', array(
			"conditions" => array("title LIKE" => sprintf("%%%s%%", $query)),
			"contain"    => array("Album" => array("Artist")),
			"fields"     => array("Album.slug", "Album.name", "Album.image", "Track.title", "Track.slug"),
			"limit"      => $limit,
			"order"		 => array("LOCATE('".$query."', Track.title)", "Track.title")
		));
	}

	public function listCurrentCollection($albumId)
	{
		$tracks = $this->find('all', array(
			"conditions" => array("album_id" => $albumId),
			"fields" => array("Track.id")
		));

		if ($tracks)
		{
			return $this->LastfmTrack->find('list', array(
				'fields' => array('LastfmTrack.mbid', 'LastfmTrack.track_id'),
				"conditions" => array("track_id" => Hash::extract($tracks, "{n}.Track.id"))
			));
		}

		return array();
	}

	public function getSnapshotsByTrackIds($ids)
	{
		$reviews = new TrackReviewSnapshot();
		return Hash::extract($reviews->find("all", array(
			"conditions" => array("track_id" => $ids)
		)), "{n}.TrackReviewSnapshot");
	}

	public function getNextTrack()
	{
		$albumId = $this->getData("Track.album_id");
		$trackIdx = $this->getData("Track.track_num");

		$track = $this->find("first", array(
			"conditions" => array(
				"album_id" => $albumId,
				"track_num >" => $trackIdx
			),
			"order" => "track_num",
			"fields" => array("Track.*")
		));

		if($track) {
			// Add the snapshot to the track
			$reviewSnapshot = $this->getSnapshotsByTrackIds(Hash::get($track, "Track.id"));
			$track["Track"]["TrackReviewSnapshot"] = count($reviewSnapshot) ? $reviewSnapshot[0] : null;
			return $track["Track"];
		}
	}

	public function getPreviousTrack()
	{
		$albumId = $this->getData("Track.album_id");
		$trackIdx = $this->getData("Track.track_num");

		$track = $this->find("first", array(
			"conditions" => array(
				"album_id" => $albumId,
				"track_num =" => $trackIdx - 1
			),
			"order" => "track_num",
			"fields" => array("Track.*")
		));

		if($track) {
			// Add the snapshot to the track
			$reviewSnapshot = $this->getSnapshotsByTrackIds(Hash::get($track, "Track.id"));
			$track["Track"]["TrackReviewSnapshot"] = count($reviewSnapshot) ? $reviewSnapshot[0] : null;
			return $track["Track"];
		}
	}

	public function saveWave($wavelength)
	{
		$this->save(array(
			"id"    => $this->getData("Track.id"),
			"wavelength"  => $wavelength
		));
	}

	public function importFromLastFm($infos)
	{
		$albumId    = $this->getData("Album.id");
		$trackData 	= array();
		$existing 	= $this->listCurrentCollection($albumId);

		foreach($infos->tracks->track as $idx => $track)
		{
			if(property_exists($track, "mbid") && trim($track->mbid) != "")
			{
				if(!array_key_exists($track->mbid, $existing))
				{
					if(property_exists($track, "duration"))
					{
						$trackData[] = array(
							"Track" => array(
								"title" => $track->name,
								"duration" => $track->duration,
								"album_id" => $albumId,
								"slug" => null,
								"track_num" => $idx+1,
								"LastfmTrack" => array(
									"mbid" => $track->mbid,
									"artist_name" => $track->artist->name
								)
							)
						);
					}
				}
			}
		}

		if(count($trackData) > 0)
		{
        	$slugs = $this->batchSlugs(Hash::extract($trackData, "{n}.Track.title"));
	     	foreach($slugs as $idx => $slug) {
        		$trackData[$idx]["Track"]["slug"] = $slug;
        	}
			return $this->saveMany($trackData, array("deep" => true));
		}
	}

	public function onReviewComplete()
	{
		// Check for achievements here.
	}

	/**
	 * Finds the current daily track challenge with related models.
	 * @return array Current daily challenge Track
	 */
	public function findDailyChallenge()
	{
		return $this->find("first", array(
			"conditions" => array("is_challenge" => true),
			"contain" => array("Album" => array( "Artist" ))
		));
	}

	/**
	 * Resets all active track challenges
	 * @return boolean True on success, False on failure
	 */
	public function resetChallenge()
	{
		return $this->updateAll(array("is_challenge" => false), array("is_challenge" => true));
	}

	/**
	 * Sets a new track challenge
	 * @param integer $trackId A valid track id
	 * @return boolean True on success, False on failure
	 */
	public function makeDailyChallenge($trackId)
	{
		return $this->updateAll(array("is_challenge" => true), array("Track.id" => $trackId));
	}

	/**
	 * Finds a new random track challenger
	 * @return array A track dataset
	 */
	public function findNewDailyChallenger()
	{
		return $this->find("first", array(
			"fields"        => array("Track.id"),
			"conditions"    => array("is_challenge" => false),
			"order"         => array("rand()")
		));
	}

	/**
	 * Returns a track and its related models based on the slug
	 * @param type $trackSlug A unique track slug
	 * @return array A track dataset
	 */
	public function getBySlugContained($trackSlug)
	{
		$this->data = $this->find("first", array(
			"conditions" 	=> array("Track.slug" => $trackSlug),
			"contain" 		=> array("LastfmTrack", "Album" => array( "Artist" ))
		));

		return $this->data;
	}

	public function getSnapshot()
	{
		$reviews = new TrackReviewSnapshot();
		return $reviews->fetch($this->getData("Track.id"));
	}

	public function getUserSnapshot($userId)
	{
		$reviews = new UserTrackReviewSnapshot();
		return $reviews->fetch($this->getData("Track.id"), $userId);
	}

	public function getUserSubscribersSnapshot($userIds)
	{
		$reviews = new SubscribersTrackReviewSnapshot();
		return $reviews->fetch($this->getData("Track.id"), $userIds);
	}

	public function toOEmbed($additionalData = array())
	{
		$reviews = new TrackReviewSnapshot();

		$data = Hash::extract($reviews->fetch($this->getData("Track.id")), "TrackReviewSnapshot");
		unset($data["id"]);
		unset($data["track_id"]);
		unset($data["snapshot_ppf"]);

		$data = Hash::insert($data, "Track.title", $this->getData("Track.title"));
		$data = Hash::insert($data, "Track.slug", $this->getData("Track.slug"));

		return parent::toOEmbed(array_merge($additionalData, $data));
	}
}

<?php

App::uses('CakeSession', 'Model/Datasource');

class Track extends AppModel
{	    
	public $hasOne = array('RdioTrack', 'TrackReviewSnapshot', 'LastfmTrack');	
    public $belongsTo = "Album";
    public $actsAs = array('Containable'); 
    
	public $validate = array(
		'title' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'A name is required'
			)
		)
	);
      
    public function getUpdatedSetBySlug($slug, $addCurrentUser = false)
    {
        $syncValues = $this->getBySlugContained($slug);
        
        if(!count($syncValues) > 0)
        {
            return false;
        }
        
        $syncValues["Artist"] = $syncValues["Album"]["Artist"];
        
        $this->LastfmTrack->data = $syncValues;        
        $this->LastfmTrack->updateCached();
                
        $this->TrackReviewSnapshot->data = $syncValues;    
        $this->TrackReviewSnapshot->updateCached();
               
        $data = $this->getBySlugContained($slug);

        if($addCurrentUser)
        {   
            $user = new User();
            $data["User"]["id"] = CakeSession::read('Auth.User.User.id');
            
            $user->UserTrackReviewSnapshot->data    = $data;
            $data["UserTrackReviewSnapshot"]        = $user->UserTrackReviewSnapshot->updateCached();      
            
            $user->SubscribersTrackReviewSnapshot->data    = $data;
            $data["SubscribersTrackReviewSnapshot"]        = $user->SubscribersTrackReviewSnapshot->updateCached();   
        }
               
        $this->data = $data;
        return $data;
    }    
	
    public function beforeSave($options = array())
    {
        $this->checkSlug(array('title'));
        return true;
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
        return $this->find("first", array(
            "conditions" => array("Track.slug" => $trackSlug),
            "contain" => array("LastfmTrack", "TrackReviewSnapshot", "RdioTrack", "Album" => array( "Artist" ))
        ));   
    }
    
    /**
     * Returns a list of tracks that are not associated to an album in TMT's catalog
     * @param array $needles A list of tracks
     * @param integer $albumId Album id that should have the tracks
     * @return array List of new tracks
     */
    public function filterNew($needles, $albumId)
    {
        $returnList = array();
        
        foreach($needles as $track)
        {   
            $returnList[] = array(
                "title" => $track->name, 
                "key" => $track->key,
                "album_id" => $albumId,
                "track_num" => $track->trackNum,
                "duration" => $track->duration,
                "RdioTrack"  => array(   
                    "key" => $track->key
                )
            );
        }
        
        return $returnList;
    }
    
    /**
     * Pull out the tracks not found on TMT and saves them.
     * @param type $tracks list of RdioTracks
     * @param integer $albumId Album id that receives the tracks
     * @return boolean True on success, False on failure
     */
    public function filterNewAndSave($tracks)
    {        
        $albumId    = $this->getData("Album.id");
        $filtered   = $this->filterNew($tracks, $albumId);
        return $this->saveMany($filtered, array('deep' => true));
    }
    
    
    public function toOEmbed()
    {
        $data = $this->getData("TrackReviewSnapshot");        
        unset($data["id"]);
        unset($data["track_id"]);
        unset($data["snapshot_ppf"]);
                
        return array(          
            "url"   => sprintf("http://%s/tracks/view/%s/", $_SERVER['SERVER_NAME'], $this->getData("Track.slug")),
            "title" => $this->getData("Track.title"),
            "data"  => $data,
            "width" => 500,
            "height" => 350,
            "html"  => '<iframe width="500" height="350" src="'.sprintf("http://%s/tracks/embed/%s/", $_SERVER['SERVER_NAME'], $this->getData("Track.slug")).'" frameborder="0"></iframe>'
        );
    }
    
    public function getOEmbedUrl()
    {
        $destination = sprintf("http://%s/tracks/view/%s/", $_SERVER['SERVER_NAME'], $this->getData("Track.slug"));
        return sprintf("http://%s/oembed?url=%s", $_SERVER['SERVER_NAME'], urlencode($destination));
    }
}
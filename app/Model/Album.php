<?php

class Album extends AppModel
{	    
	public $hasOne      = array('RdioAlbum', 'AlbumReviewSnapshot', "LastfmAlbum");
    public $hasMany     = array('Tracks' => array('order' => 'track_num ASC'));
    public $belongsTo   = "Artist";
    	
    public function beforeSave($options = array())
    {
        // Ensure the data has a valid unique slug
        $this->checkSlug(array('name'));
        return true;
    }
    
    /**
     * Filters Rdio albums not currently saved on TMT found in $albums
     * @param integer $artistId
     * @param string $rdioKey
     * @param array $albums A dataset of Albums returned by Rdio
     * @return array A list containing the new albums
     */
    public function filterNewRdioAlbums($artistId, $rdioKey, $albums)
    {
        return $this->RdioAlbum->filterNew($artistId, $rdioKey, $albums);
    }
    
    /**
     * Saves a list of RdioAlbums.
     * @param type $artistId
     * @param type $rdioKey
     * @param array $albums A dataset of Albums returned by Rdio
     * @return boolean True on success, false on failure
     */
    public function saveDiscography($artistId, $rdioKey, $albums)
    {
        $filtered = $this->filterNewRdioAlbums($artistId, $rdioKey, $albums);
        return $this->saveMany($filtered, array('deep' => true));                       
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
            "conditions" => array("Album.is_newrelease" => true),
            "limit" => $limit,
            "order" => array("Album.release_date DESC")
        )); 
    }
}
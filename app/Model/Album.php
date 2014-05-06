<?php

App::uses('User', 'Model');
App::uses('CakeSession', 'Model/Datasource');   
App::uses('OEmbedable', 'Model');

class Album extends OEmbedable
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
            "fields"    => array("Album.id", "Album.name", "RdioAlbum.*", "Artist.name", "LastfmAlbum.id", "LastfmAlbum.lastsync", "AlbumReviewSnapshot.*", "RdioAlbum.*")
        ));

        if(count($syncValues)) {
            $this->RdioAlbum->data = $syncValues;        
            $this->RdioAlbum->updateCached();
                          
            $this->LastfmAlbum->data = $syncValues;        
            $this->LastfmAlbum->updateCached();
                   
            $this->AlbumReviewSnapshot->data = $syncValues;    
            $this->AlbumReviewSnapshot->updateCached();
                   
            $data = $this->findBySlug($slug);
            
            if($addCurrentUser)
            {   
                $user = new User();
                $data["User"]["id"] = CakeSession::read('Auth.User.User.id');
                
                $user->SubscribersAlbumReviewSnapshot->data    = $data;
                $data["SubscribersAlbumReviewSnapshot"]        = $user->SubscribersAlbumReviewSnapshot->updateCached();        
                
                $user->UserAlbumReviewSnapshot->data    = $data;
                $data["UserAlbumReviewSnapshot"]        = $user->UserAlbumReviewSnapshot->updateCached();            
            }
            
            $this->data = $data;
            return $data;
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
        $data = $this->getData("AlbumReviewSnapshot");
        unset($data["album_id"]);
        unset($data["id"]);
        unset($data["metacritic_score"]);
        unset($data["snapshot_ppf"]);
     
        return parent::toOEmbed(array_merge($additionalData, $data));
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
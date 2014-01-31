<?php
App::uses('User', 'Model');
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
    
    public function getUpdatedSetBySlug($slug, $addCurrentUser = false)
    {
        $syncValues = $this->find("first", array(
            "conditions" => array("Album.slug" => $slug),
            "fields"    => array("Album.id", "Album.name", "RdioAlbum.*", "Artist.name", "LastfmAlbum.id", "LastfmAlbum.lastsync", "AlbumReviewSnapshot.*")
        ));
                
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
            
            $user->SubscribersAlbumReviewSnapshot->data    = $data;
            $data["SubscribersAlbumReviewSnapshot"]        = $user->SubscribersAlbumReviewSnapshot->updateCached();        
            
            $user->UserAlbumReviewSnapshot->data    = $data;
            $data["UserAlbumReviewSnapshot"]        = $user->UserAlbumReviewSnapshot->updateCached();            
        }
        
        $this->data = $data;
        return $data;
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
            "conditions" => array("Album.is_newrelease" => true),
            "limit" => $limit,
            "order" => array("Album.release_date DESC")
        )); 
    }
    
    public function toOEmbed()
    {
        $data = $this->getData("AlbumReviewSnapshot");
        unset($data["album_id"]);
        unset($data["id"]);
        unset($data["metacritic_score"]);
        unset($data["snapshot_ppf"]);
        
        return array(          
            "url"   => sprintf("http://%s/albums/view/%s/", $_SERVER['SERVER_NAME'], $this->getData("Album.slug")),
            "title" => $this->getData("Album.name"),
            "data"  => $data,
            "width" => 500,
            "height" => 350,
            "html"  => '<iframe width="500" height="350" src="'.sprintf("http://%s/albums/embed/%s/", $_SERVER['SERVER_NAME'], $this->getData("Album.slug")).'" frameborder="0"></iframe>'
        );
    }    
    
    public function getOEmbedUrl()
    {
        $destination = sprintf("http://%s/albums/view/%s/", $_SERVER['SERVER_NAME'], $this->getData("Album.slug"));
        return sprintf("http://%s/oembed?url=%s", $_SERVER['SERVER_NAME'], urlencode($destination));
    }
}
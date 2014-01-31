<?php

class RdioTrack extends AppModel
{	
	public $belongsTo = array('Track');
        
    public function listCurrentCollection($rdioAlbumId)
    {        
        return $this->findById($rdioAlbumId, array('RdioTrack.key', 'RdioTrack.track_id'));
    }
    
}
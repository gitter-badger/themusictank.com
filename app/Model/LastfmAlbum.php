<?php

App::uses('Track', 'Model');

class LastfmAlbum extends AppModel
{
	public $belongsTo   = array('Album');
    public $actsAs      = array('Lastfm', 'ThumbnailLeech');

    public function getExpiredRange()
    {
    	return time() - WEEK;
    }

    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $artistName = $this->getData("Artist.name");
            $albumName  = $this->getData("Album.name");
            $infos      = $this->getLastFmAlbumDetails($artistName, $albumName);

            if($infos)
            {
                $this->_saveDetails($infos);
                return $infos;
            }
        }
        return $this->data;
    }

    public function requiresUpdate()
    {
        $timestamp = (int)Hash::get($this->data, "LastfmAlbum.lastsync");
        return $timestamp < $this->getExpiredRange();
    }

    private function _saveDetails($infos)
    {
        $albumId        = $this->getData("Album.id");
        $lastfmAlbumId  = $this->getData("LastfmAlbum.id");
        $image          = Hash::get($this->data, "LastfmAlbum.image");

        $this->saveMany(array(
            "LastfmAlbum" => array(
                "id"        => $lastfmAlbumId,
                "mbid" => $infos->mbid,
                "lastsync"  => time(),
                "wiki"      => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content),
	            "Album" => array(
	                "id"  => $albumId,
	                "slug"  => $this->getData("Album.slug"),
	                "release_date" => strtotime(trim($infos->releasedate)),
	                "release_date_text" => $infos->releasedate,
		            "image"     => empty($infos->image[4]->{'#text'}) ? null : $this->getImageFromUrl($infos->image[4]->{'#text'}, $image),
		            "image_src" => empty($infos->image[4]->{'#text'}) ? null : $infos->image[4]->{'#text'}
	            )
            )
        ), array("deep" => true));

        $Track = new Track();
        $Track->data = $this->data;
        $Track->data["Album"] = array("id" => $albumId);
        $Track->importFromLastFm($infos);
    }

    public function listCurrentCollection()
    {
        return $this->find('list', array(
            'fields' => array('LastfmAlbum.mbid', 'LastfmAlbum.album_id')
        ));
    }

    public function filterNew($needles)
    {
        $currentList        = $this->listCurrentCollection();
        $listBeingParsed    = array();
        $returnList         = array();

        foreach($needles->album as $album)
        {
            // only save albums of interest
            if(property_exists($album, "mbid") && trim($album->mbid) != "")
            {
                // Add the artist to the global collection if
                // its a new artist
                if(!array_key_exists($album->mbid, $currentList))
                {
                    // Also make sure there are no doubles inside the possible new stack
                    if(!in_array($album->mbid, $listBeingParsed))
                    {
                        $returnList[] = array(
                            "Album" => array(
                                "name" => $album->name,
                            ),
                            "LastfmAlbum"  => array(
                                "mbid" => $album->mbid,
                                "artist_name" => $album->artist,
                                "artist_id" => null
                            )
                        );

                        $listBeingParsed[] = $album->mbid;
                    }
                }
            }
        }

        return $returnList;
    }

}

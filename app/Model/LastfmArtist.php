<?php

class LastfmArtist extends AppModel
{
	public $belongsTo = array('Artist');
    public $actsAs = array('Lastfm');

    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $artistName = $this->getData("Artist.name");

            $data = $this->data;
            $infos = $this->getArtistBiography($artistName);
            if($infos)
            {
                $data["LastfmArtist"] = $this->_saveDetails($infos);
            }

            $ranks = $this->getArtistTopAlbums($artistName);
            if($ranks)
            {
                $this->Artist->Albums->LastfmAlbum->data = $data;
                $this->Artist->Albums->LastfmAlbum->saveNotableAlbums($ranks);
            }

            $this->data = $data;
        }
    }

    public function requiresUpdate()
    {
        $timestamp = $this->getData("LastfmArtist.lastsync");
        return $timestamp + WEEK < time();
    }

    private function _saveDetails($infos)
    {
        $artistId       = $this->getData("Artist.id");
        $lastfmArtistId = $this->getData("LastfmArtist.id");
        $image          = $this->getData("LastfmArtist.image");

        $newRow         = array(
            "id"        => $lastfmArtistId,
            "artist_id" => $artistId,
            "lastsync"  => time(),
            "image"     => empty($infos->image[4]->{'#text'}) ? null : $this->getImageFromUrl($infos->image[4]->{'#text'}, $image),
            "image_src" => empty($infos->image[4]->{'#text'}) ? null : $infos->image[4]->{'#text'},
            "biography" => empty($infos->bio->summary) ? __("Biography is not available at this time.") : $this->cleanLastFmWikiText($infos->bio->content),
            "url"       => $infos->url
        );

        return $this->save($newRow) ? $newRow : false;
    }

}

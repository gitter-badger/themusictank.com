<?php

class LastfmTrack extends AppModel
{
	public $belongsTo = array('Track');
    public $actsAs = array('Lastfm');

    public function updateCached()
    {
        if($this->requiresUpdate())
        {
            $trackTitle = $this->getData("Track.title");
            $artistName = $this->getData("Artist.name");
            $infos = $this->getLastFmTrackDetails($trackTitle, $artistName);

            if($infos)
            {
                $this->_saveDetails($infos);
            }
        }
    }

    public function requiresUpdate()
    {
        $timestamp = $this->getData("LastfmTrack.lastsync");
        return $timestamp + WEEK < time();
    }

    private function _saveDetails($infos)
    {
        $trackId       = $this->getData("Track.id");
        $lastfmTrackId = $this->getData("LastfmTrack.id");

        $newRow         = array(
            "id"        => $lastfmTrackId,
            "track_id"  => $trackId,
            "lastsync"  => time(),
            "wiki"      => empty($infos->wiki->content) ? null : $this->cleanLastFmWikiText($infos->wiki->content)
        );

        return $this->save($newRow);
    }
}

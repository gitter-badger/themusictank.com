<?php
App::uses('Album', 'Model');
App::uses('Track', 'Model');
App::uses('LastfmArtist', 'Model');
class Config extends AppModel
{
    public function getByKey($key)
    {
		$this->data = $this->findByKey($key);
		return $this->data;
    }

    public function updateTrackChallenge()
    {
		$this->getByKey("last_trackchallenge_sync");
    	if($this->requiresDailyTrackChallengeUpdate())
        {
            // Fetch before resetting to make sure we don't select the same one.
            $track = new Track();
            $newTrack = $track->findNewDailyChallenger();
            if(count($newTrack) > 0)
            {
                // Reset previous track challenge and save the status on the new track
                $track->resetChallenge();
                $track->makeDailyChallenge($newTrack["Track"]["id"]);
                $this->setTrackChallengeUpdate();
            }
        }
    }

    public function updatePopularArtists()
    {
		$this->getByKey("last_popularartist_sync");
    	if($this->requiresPopularArtistsUpdate())
        {
            $lastfmArtist = new LastfmArtist();
            if($lastfmArtist->updatePopular()) {
                $this->setPopularArtistUpdate();
            }
        }
    }

    /**
     * Specifies if the track challenge needs to be updated.
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresDailyTrackChallengeUpdate()
    {
    	$lastsync = (int)Hash::get($this->data, "Config.value");
    	return $lastsync < (time() - 60*60*23.5); // leave half an hour of breathing room for the cron.
    }

    public function requiresPopularArtistsUpdate()
    {
    	$lastsync = (int)Hash::get($this->data, "Config.value");
    	return $lastsync < (time() - 60*60*23.5); // leave half an hour of breathing room for the cron.
    }

    /**
     * Updates the track challenge sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setTrackChallengeUpdate()
    {
        return $this->_setSyncUpdate("last_trackchallenge_sync");
    }

    public function setPopularArtistUpdate()
    {
        return $this->_setSyncUpdate("last_popularartist_sync");
    }

    protected function _setCronUpdate($key)
    {
        return $this->_setSyncUpdate($key);
    }

    public function setCronStart($which)
    {
    	$key = "cron_".$which."_start";
    	$this->getByKey($key);
 		$this->_setCronUpdate($key);
    }

    public function setCronEnd($which)
    {
    	$key = "cron_".$which."_end";
    	$this->getByKey($key);
 		$this->_setCronUpdate($key);
    }

    private function _setSyncUpdate($key)
    {
    	$lastId = (int)Hash::get($this->data, "Config.id");
        return $this->save(array(
            "id"        => ($lastId > 0) ? $lastId : null,
            "key"       => $key,
            "value"     => time()
        ));
    }
}

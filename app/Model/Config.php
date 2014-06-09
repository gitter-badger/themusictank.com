<?php

App::uses('Track', 'Model');
App::uses('Album', 'Model');
App::uses('Artist', 'Model');
App::uses('LastfmArtist', 'Model');

class Config extends AppModel
{
    const TYPE_DAILY    = "daily";
    const TYPE_WEEKLY   = "weekly";
    const TYPE_MONTHLY  = "monthly";
    const TYPE_YEARLY   = "yearly";

    public function updateCachedDaily()
    {
        $data = $this->getDaily();
        $this->data = $data;

        if($this->requiresPopularArtistUpdate())
        {
            // Make sure we have the new artists
            $lastfmArtist = new LastfmArtist();
            if($lastfmArtist->updatePopular()) {
                $this->setPopularArtistUpdate();
            }
        }

        $this->data = $data;
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

    public function updateCachedWeekly()
    {
        $data = $this->getWeekly();
        $this->data = $data;

        if($this->requiresNewReleasesUpdate())
        {/*
        	Lastfm doesn't allow
            $artist = new Artist();
            $album = new Album();
            $newReleases = $this->getRdioNewReleases("twoweeks");

            if(count($newReleases) > 0)
            {
                // Save the new artists
                $artist->filterNewAndSave($newReleases);

                // Get all the rdio artist keys that will require an album update
                $rdioArtistKeys = array();
                $rdioAlbumKeys = array();
                foreach($newReleases as $release)
                {
                    $rdioArtistKeys[] = $release->artistKey;
                    $rdioAlbumKeys[] = $release->key;
                }

                $rArtistData = $artist->RdioArtist->getListByKeys($rdioArtistKeys);

                foreach($newReleases as $release)
                {
                    $album->data = array(
                        "Artist"        => array("id" => $rArtistData[$release->artistKey]),
                        "RdioArtist"    => array("key" => $release->artistKey)
                    );
                    $album->saveDiscography(array($release));
                }

                $album->resetNewReleases();
                $album->setNewReleases(array_values($album->RdioAlbum->getListByKeys($rdioAlbumKeys)));

                $this->setNewReleasesUpdate();
            }
            */
        }
    }

    /**
     * Preloads all the configuration options related to a daily update
     * @return array Dataset
     */
    public function getDaily()
    {
        return $this->find("all", array(
            "conditions" => array("Config.key" => array(
                "last_heavyrotation_sync",
                "last_trackchallenge_sync"
            ))
        ));
    }

    /**
     * Preloads all the configuration options related to a weekly update
     * @return array Dataset
     */
    public function getWeekly()
    {
        return $this->find("all", array(
            "conditions" => array("Config.key" => array(
                "last_newreleases_sync",
                "last_weeklychart_sync"
            ))
        ));
    }

    /**
     * Updates the last heavy rotation sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setNewReleasesUpdate()
    {
        return $this->_setSyncUpdate("last_newreleases_sync");
    }

    /**
     * Updates the last heavy rotation sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setPopularArtistUpdate()
    {
        return $this->_setSyncUpdate("last_heavyrotation_sync");
    }

    /**
     * Updates the track challenge sync time to now
     * @return boolean True on success, False on failure.
     */
    public function setTrackChallengeUpdate()
    {
        return $this->_setSyncUpdate("last_trackchallenge_sync");
    }

    public function setWeeklyTrackChartUpdate()
    {
        return $this->_setSyncUpdate("last_weeklychart_sync");
    }

    /**
     * Specifies if the popular artists need to be updated.
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresPopularArtistUpdate()
    {
        return $this->_validateDelay("last_heavyrotation_sync", 60*60*24);
    }

    /**
     * Specifies if the popular artists need to be updated.
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresNewReleasesUpdate()
    {
        return $this->_validateDelay("last_newreleases_sync", 60*60*24*7);
    }

    /**
     * Specifies if the track challenge needs to be updated.
     * @return boolean True is content is not up to date, False if it is
     */
    public function requiresDailyTrackChallengeUpdate()
    {
        return $this->_validateDelay("last_trackchallenge_sync", 60*60*24);
    }

    /**
     * Based on preloaded values, fetches a unique record. This internally
     * allows only one query to update the config
     * @param string $key The unique config key
     * @return array Correct row on success, null on failure
     */
    private function _getRecordByKey($key)
    {
        foreach($this->data as $config)
        {
            if($config["Config"]["key"] == $key)
            {
                return $config;
            }
        }
        return null;
    }

    private function _setSyncUpdate($key)
    {
        $record = $this->_getRecordByKey($key);
        return $this->save(array(
            "id"        => !is_null($record) ? $record["Config"]["id"] : null,
            "key"       => $key,
            "value"     => time()
        ));
    }

    private function _validateDelay($key, $syncDelay)
    {
        $record = $this->_getRecordByKey($key);
        if($record)
        {
            return (int)$record["Config"]["value"] + $syncDelay < time();
        }

        return true;
    }

}

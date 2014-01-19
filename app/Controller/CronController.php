<?php
/**
 * CronController controller
 *
 * Contains Cron pages methods
 *
 * @package       app.Controller
 */
class CronController extends AppController {
        
	var $components = array("RdioApi", "Email");
    
    /** 
     * Actions that are expected to run daily.
     */
    public function daily()
    {
        $this->layout = "blank";
        $this->loadModel("Config");    
        $feedback   = array("This is the daily");
        
        $data = $this->Config->getDaily();        
        if($this->Config->requiresPopularArtistUpdate($data))
        {
            $feedback[] = ($this->_syncPopular() !== false) ?
                "[OK]  Popular artists have been updated successfully" :
                "[Err] Unsuccessful attempt at updating popular artists";
        }
        else
        {
            $feedback[] = "[SKIP] Do not have to update popular artists";
        }
                        
        if($this->Config->requiresDailyTrackChallengeUpdate($data))
        {
            $feedback[] = ($this->_syncDailyTrackChallenge() !== false) ?
                "[OK]  Daily track challenge has been updated successfully" :
                "[Err] Unsuccessful attempt at updating track challenge";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update daily track challenge";
        }
        
        
        $this->_printOut($feedback);        
        $this->render('/Pages/cron/');
    }
    
    public function weekly()
    {
        $this->layout = "blank";
        $this->loadModel("Config");        
        $feedback   = array("This is the weekly");
                
        
        $data = $this->Config->getWeekly();        
        if($this->Config->requiresNewReleasesUpdate($data))
        {
            $feedback[] = ($this->_syncNewReleases() !== false) ?
                "[OK]  New releases have been updated successfully" :
                "[Err] Unsuccessful attempt at updating new releases";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update new releases";
        }
        /*
        if($this->Config->requiresWeeklyTrackChartsUpdate($data))
        {
            $feedback[] = ($this->_syncWeeklyTrackCharts() !== false) ?
                "[OK]  Charts (tracks) have been updated successfully" :
                "[Err] Unsuccessful attempt at updating charts (tracks)";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update charts (tracks)";
        }        
        
        if($this->Config->requiresWeeklyAlbumChartsUpdate($data))
        {
            $feedback[] = ($this->_syncWeeklyAlbumCharts() !== false) ?
                "[OK]  Charts (albums) have been updated successfully" :
                "[Err] Unsuccessful attempt at updating charts (albums)";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update charts (albums)";
        }  */
        
        $this->_printOut($feedback);        
        $this->render('/Pages/cron/');
    }
        
    /*
    public function monthly()
    {
        $this->layout = "blank";
        $this->loadModel("Config");        
        $feedback   = array("This is the monthly");
                
        
        $data = $this->Config->getMonthly();
        
        /f($this->Config->requiresMonthlyChartsUpdate($data))
        {
            $feedback[] = ($this->_syncMonthlyCharts() !== false) ?
                "[OK]  Charts have been updated successfully" :
                "[Err] Unsuccessful attempt at updating charts";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update charts";
        }
        
        
        $this->_printOut($feedback);        
        $this->render('/Pages/cron/');
    }
    
    public function yearly()
    {
        $this->layout = "blank";
        $this->loadModel("Config");        
        $feedback   = array("This is the yearly");
                
        
        $data = $this->Config->getYearly();
        
        
        if($this->Config->requiresYearlyChartsUpdate($data))
        {
            $feedback[] = ($this->_syncYearlyCharts() !== false) ?
                "[OK]  Charts have been updated successfully" :
                "[Err] Unsuccessful attempt at updating charts";
        }   
        else
        {
            $feedback[] = "[SKIP] Do not have to update charts";
        }
        
        
        $this->_printOut($feedback);        
        $this->render('/Pages/cron/');
    }
    */
    
    /** 
     * Populated the review frames with dummy test values. Should never be used in production
     
    public function fillReviewFrames()
    {
        if(Configure::read('debug') > 0)
        {
            $this->loadModel("Album");
            $this->loadModel("ReviewFrames");
            set_time_limit(0);
            
            $data = $this->Album->find("all", array(
                "conditions" => array("Artist.slug" => "children-of-bodom")
            ));
            
            if(count($data) > 0)
            {
                foreach($data as $album)
                {                    
                    if(count($album["Tracks"]) > 0)
                    {
                        foreach($album["Tracks"] as $track)
                        {
                            $reviewId = uniqid();
                            $i = (int)$track["duration"];                            
                            $reviewFramesData = array();
                            while($i--)
                            {                                
                                $k = 24; // 24 fps
                                $value = 1 - (rand(0, 1000) / 1000);
                                while($k--)
                                {          
                                    $reviewFramesData[] = array(
                                        "artist_id" => $album["Artist"]["id"],
                                        "album_id"  => $album["Album"]["id"],
                                        "track_id"  => $track["id"],
                                        "user_id"   => 1,
                                        "review_id" => $reviewId,
                                        "groove"    => $value,
                                        "starpowering" => 0,
                                        "suckpowering" => 0,
                                        "multiplier" => 0,
                                        "position" => $i
                                    );
                                }
                            }
                            
                            $this->ReviewFrames->saveMany($reviewFramesData);
                        }
                        
                    }
                }
            }
            
            $this->render('/Pages/cron/');    
        }
    }    */
    
    /** 
     * Based on whether or not the site in is debug mode, this will email a transaction
     * digest or will output the contents with on the page.
     * 
     * @private
     * @param array $feeback An array of string containing debugging information.
     */
    private function _printOut($feedback)
    {
        $isDebug    = (Configure::read('debug') > 0);
        if(count($feedback) > 0)
        {
            $today      = date("F j, Y, g:i a");
            $message    = array_merge(
                array("The Music Tank: Cron digest", "Today is $today", ""),
                $feedback,
                array("")
            );
                
            if($isDebug)
            {
                debug($message);
            }
            else
            {
                $this->Email->from    = "The Music Tank <support@themusictank.com>";
                $this->Email->to      = "Francois Faubert <frank@francoisfaubert.com>";
                $this->Email->subject = "The Music Tank: Cron digest for $today";
                $this->Email->send(implode("\n", $message));
            }
        }
    }
    
    /** 
     * Syncs the currently popular Rdio artists.
     * 
     * @private
     */
    private function _syncPopular()
    { 
        $this->loadModel("Artist");    

        // Make sure we have the new artists
        $popularArtists = $this->RdioApi->getHeavyRotation();
        if($popularArtists)
        {
            // Reset popular artists
            $this->Artist->RdioArtist->resetPopular();    
            $this->Artist->filterNewAndSave($popularArtists);
            $this->Artist->RdioArtist->makePopular($popularArtists); 
            
            return $this->Config->setPopularArtistUpdate();
        } 
        
        return false;
    }        
    
    /** 
     * Updates the track challenge with a random track.
     * 
     * @private
     */
    private function _syncDailyTrackChallenge()
    {      
        $this->loadModel("Track");
        
        // Fetch before resetting to make sure we don't select the same one.        
        $newTrack = $this->Track->findNewDailyChallenger();
        if(count($newTrack) > 0)
        {
            // Reset previous track challenge and save the status on the new track
            $this->Track->resetChallenge();
            $this->Track->makeDailyChallenge($newTrack["Track"]["id"]);
            
            return $this->Config->setTrackChallengeUpdate();      
        }
        
        return false;
    }
    
    /** 
     * Updates the new releases form rdio.
     * 
     * @private
     */
    private function _syncNewReleases()
    {      
        $this->loadModel("Album");
        $this->loadModel("Artist");
          
        $newReleases = $this->RdioApi->getNewReleases("twoweeks");
        if(count($newReleases) > 0)
        {
            // Save the new artists
            $this->Artist->filterNewAndSave($newReleases);
                        
            // Get all the rdio artist keys that will require an album update
            $rdioArtistKeys = array();
            $rdioAlbumKeys = array();
            foreach($newReleases as $release)
            {
                $rdioArtistKeys[] = $release->artistKey;
                $rdioAlbumKeys[] = $release->key;
            }            
                       
            $rArtistData = $this->Artist->RdioArtist->getListByKeys($rdioArtistKeys);            
           
            foreach($newReleases as $release)
            {
                $this->Album->saveDiscography($rArtistData[$release->artistKey], $release->artistKey, array($release));
            }
                        
            $this->Album->resetNewReleases();
            $this->Album->setNewReleases(array_values($this->Album->RdioAlbum->getListByKeys($rdioAlbumKeys)));

            return $this->Config->setNewReleasesUpdate();
        }
        
        return false;
    }
    /*
    private function _syncWeeklyTrackCharts()
    {      
        $this->loadModel("TrackReviewSnapshot");
        $this->loadModel("Chart");
        
        $year       = date("Y");
        $weekNumber = date("W");
        
        if($weekNumber - 1 > 0)
        {
            $previousWeek = $weekNumber - 1;
            $previousYear = $year;
        }
        else
        {
            $previousYear = $year - 1;
            $previousWeek = 52;
        }        
        
        $endTimestamp   = strtotime($year . "W" . $weekNumber);  
        $startTimestamp = strtotime($previousYear . "W" . $previousWeek);
                
        $tracksChart    = $this->Chart->generate($this->TrackReviewSnapshot, $startTimestamp, $endTimestamp, 100);        
        $this->Chart->saveWeekly($tracksChart);        
        
        return $this->Config->setWeeklyChartsUpdate();
    }
    
 private function _syncWeeklyAlbumCharts()
    {      
        $this->loadModel("AlbumReviewSnapshot");
        $this->loadModel("Chart");
        
        $year       = date("Y");
        $weekNumber = date("W");
        
        if($weekNumber - 1 > 0)
        {
            $previousWeek = $weekNumber - 1;
            $previousYear = $year;
        }
        else
        {
            $previousYear = $year - 1;
            $previousWeek = 52;
        }        
        
        $endTimestamp   = strtotime($year . "W" . $weekNumber);  
        $startTimestamp = strtotime($previousYear . "W" . $previousWeek);
                
        $albumsChart    = $this->Chart->generate($this->AlbumReviewSnapshot, $startTimestamp, $endTimestamp, 100);
        
        $this->Chart->saveWeekly($albumsChart);        
        
        return $this->Config->setWeeklyChartsUpdate();
    }
    */
}

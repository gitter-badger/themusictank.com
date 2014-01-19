<?php
/**
 * ChartsController controller
 *
 * Contains Charts pages methods
 *
 * @package       app.Controller
 */
class ChartsController extends AppController {
                        
    /** 
     * Chart landing page. Displays The current TMT chart
     */
    public function index()
    {                
        $this->loadModel("Album");
        $this->loadModel("Track");
        
        $weekStartTimestamp = mktime(0, 0, 0, date("n"), date("j") - date("N"));
                
        $this->set("weekStart", date("l jS Y", $weekStartTimestamp));
        
        $this->set("albumsChart", $this->Album->AlbumReviewSnapshot->getChart($weekStartTimestamp, time(), 10));
        $this->set("tracksChart", $this->Track->TrackReviewSnapshot->getChart($weekStartTimestamp, time(), 10));
        
        $this->setPageTitle(__("Charts"));        
        $this->setPageMeta(array(
            "keywords" => array(__("Album charts"), __("Reviews"), __("Ranking")),
            "description" => __("Listing of the top 100 albums and tracks in The Music Tank's reviewing pool.")
        ));
    }
    
    public function weekly($type, $year, $weekNumber)
    {
        $startTimestamp = strtotime($year . "W" . $weekNumber);  
        $endTimestamp   = strtotime($year . "W" . ($weekNumber+1));
        
        $this->set("weekStart", date("l jS Y", $startTimestamp));
        $this->set("weekEnd", date("l jS Y", $endTimestamp));
                
        $this->set("currentWeek", array($year, $weekNumber));
        
        ($weekNumber - 1 > 0) ?
            $this->set("previousWeek", array($year, $weekNumber - 1)) :
            $this->set("previousWeek", array($year - 1, 52));
        
        ($weekNumber + 1 <= 52) ?
            $this->set("nextWeek", array($year, $weekNumber + 1)) :
            $this->set("nextWeek", array($year + 1, 1));
                
        switch($type)
        {
            case "tracks" : 
                $this->_getTracks($startTimestamp, $endTimestamp);
                break;
            case "albums" :                
                $this->_getAlbums($startTimestamp, $endTimestamp);
                break;
        }
    }        
    
    private function _getAlbums($startTimestamp, $endTimestamp)
    {                
        $this->loadModel("Album");                            
        $this->set("albumCharts", $this->Album->AlbumReviewSnapshot->getChart($startTimestamp, $endTimestamp));
        
        $this->setPageTitle(__("Top 100 Albums"));        
        $this->setPageMeta(array(
            "keywords" => array(__("Album charts"), __("Reviews"), __("Ranking")),
            "description" => __("Listing of the top 100 albums in The Music Tank's reviewing pool.")
        ));
    }
    
    private function _getTracks($startTimestamp, $endTimestamp)
    {                
        $this->loadModel("Track");                       
        $this->set("trackCharts", $this->Track->TrackReviewSnapshot->getChart($startTimestamp, $endTimestamp));
        
        $this->setPageTitle(__("Top 100 Tracks"));        
        $this->setPageMeta(array(
            "keywords" => array(__("Tracks charts"), __("Reviews"), __("Ranking")),
            "description" => __("Listing of the top 100 tracks in The Music Tank's reviewing pool.")
        ));
    }
    
}
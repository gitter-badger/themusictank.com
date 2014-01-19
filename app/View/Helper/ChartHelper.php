<?php

class ChartHelper extends AppHelper {
    
    var $helpers = array('Html');
    
    private $_pies = array();
    private $_charts = array();
    
    const CURVE_MINIMUM_INDEX = 0;
    const CURVE_AVERAGE_INDEX = 1;
    const CURVE_MAXIMUM_INDEX = 2;
        
    
    public function formatScore($score)
    {
        // @Todo : colorize by the amount of good
        if(!is_null($score) && $score > 0)
            return round($score * 100) . "%";
        
        return "N/A";
    }

    public function getEnjoymentTime($data, $duration)
    {        
        $lblMins = "\\" . implode("\\", str_split(__("mins")));
        $lblSecs = "\\" . implode("\\", str_split(__("secs")));
        
        return array(
            "liking"       => date("i $lblMins s $lblSecs", $duration * ($data["liking_pct"] / 100)),
            "disliking"    => date("i $lblMins s $lblSecs", $duration * ($data["disliking_pct"] / 100))
        );
    }           
    
    public function getSmallPie($type, $key, $data)
    {
        /*$this->_pies["$type-$key"] = json_encode(array(
            array("", (int)$data["disliking_pct"] + (int)$data["neutral_pct"]),
            array(__("Like"), (int)$data["liking_pct"])
        ));
        return "<div class=\"appreciation-box\">
                    <div class=\"col chart chart-small pie appreciation-pie\" id=\"pie-$type-$key\"></div>
                    <div class=\"col\">".(int)$data["liking_pct"]."%</div>
               </div>";*/        
        
        return '<div class="square" style="display:inline-block; width:60px; height:40px; position:relative;">
                    <div style="background:#7da0db; height:'. ($data["liking_pct"] ? $data["liking_pct"] : 0) .'%; overflow:hidden;"></div>
                    <div style="background:#28416b; height:'. ($data["neutral_pct"] ? $data["neutral_pct"] : 100) .'%; overflow:hidden;"></div>
                    <div style="background:#0b1c38; height:'. ($data["disliking_pct"] ? $data["neutral_pct"] : 0).'%; overflow:hidden;"></div>
                </div>';
    }

    public function getBigPie($type, $key, $data)
    {
        
        /* Round pie 
        $this->_pies["$type-$key"] = json_encode(array(
            array(__("Dislike"), (int)$data["disliking_pct"]),
            array(__("Neutral"), (int)$data["neutral_pct"]),
            array(__("Like"), (int)$data["liking_pct"])
        ));
        return "<div class=\"pie appreciation-pie\" id=\"pie-$type-$key\"></div>";*/
        
        /* square pie */
        return '<div class="square" style="width:200px; height:200px; position:relative;">
                    <div style="background:#7da0db; height:'.$data["liking_pct"].'%; overflow:hidden;">'.__('Liking').'</div>
                    <div style="background:#28416b; height:'.$data["neutral_pct"].'%; overflow:hidden;">'.__('Neutral').'</div>
                    <div style="background:#0b1c38; height:'.$data["disliking_pct"].'%; overflow:hidden;">'.__('Disliking').'</div>
                </div>';
    }
    
    public function getArtistChart($key, $data)
    {
        $formatted = array("range" => array(), "groove" => array());
                        
        foreach(json_decode($data["curve_snapshot"]) as $idx => $album)
        {
            $label = $idx;
            if($album)
            {
                foreach($album as $reviewFrame)
                {        
                    $formatted["range"][]   = array($label, $reviewFrame[ self::CURVE_MAXIMUM_INDEX ], $reviewFrame[ self::CURVE_MINIMUM_INDEX ]);
                    $formatted["groove"][]  = array($label, $reviewFrame[ self::CURVE_AVERAGE_INDEX ]);
                }
            }
        }
                
        $this->_charts["artist-$key"] = json_encode($formatted);                
        return "<div class=\"chart groove artist-groove\" id=\"chart-artist-$key\"></div>";
    }
    
    public function getAlbumChart($key, $data)
    {
        $formatted = array("range" => array(), "groove" => array());
        
        foreach(json_decode($data["curve_snapshot"]) as $idx => $track)
        {
            if(isset($track))
            {
                foreach($track as $reviewFrame)
                {
                    $label = ($idx % 10 === 0) ? date("i:s", $idx * $data['snapshot_ppf'])  : "";            
                    $formatted["range"][]   = array($label, $reviewFrame[ self::CURVE_MAXIMUM_INDEX ], $reviewFrame[ self::CURVE_MINIMUM_INDEX ]);
                    $formatted["groove"][]  = array($label, $reviewFrame[ self::CURVE_AVERAGE_INDEX  ]);
                }
            }
        }
        
        $this->_charts["album-$key"] = json_encode($formatted);                
        return "<div class=\"chart groove album-groove\" id=\"chart-album-$key\"></div>";
    }

    public function getTrackChart($key, $data)
    {
        $formatted = array("range" => array(), "groove" => array());
        $snapshot = $this->_formatSnapshot($data);
        
        foreach($snapshot as $idx => $reviewFrame)
        {
            $label = ($idx % 10 === 0) ? date("i:s", $idx * $data['snapshot_ppf'])  : "";            
            $formatted["range"][]   = array($label, $reviewFrame[ self::CURVE_MAXIMUM_INDEX ], $reviewFrame[ self::CURVE_MINIMUM_INDEX ]);
            $formatted["groove"][]  = array($label, $reviewFrame[ self::CURVE_AVERAGE_INDEX ]);
        }
        
        $this->_charts["track-$key"] = json_encode($formatted);                
        return "<div class=\"chart groove track-groove\" id=\"chart-track-$key\"></div>";
    }
        
    
    public function getReviewFramesGroove($key, $data)
    {
        $formatted = array("groove" => array());
        $workingSet = $data;
        
        foreach($workingSet as $reviewFrame)
        {
            $label = ($reviewFrame["position"] % 10 === 0) ? date("i:s", $reviewFrame["position"])  : "";          
            $formatted["groove"][]  = array($label, (float)$reviewFrame["groove"]);
        }
        
        $this->_charts["rf-$key"] = json_encode($formatted);                
        return "<div class=\"chart groove rf-groove\" id=\"chart-rf-$key\"></div>";
    }
    
    public function getScript()
    {
        $str = array();
        foreach($this->_pies as $key => $pie) $str[] = "tmt.pie(\"$key\", $pie);";
        foreach($this->_charts as $key => $chart) $str[] = "tmt.chart(\"$key\", $chart);";
        
        if(count($str) > 0)
        {
            return "<script>$(function(){\n" . implode("\n", $str) . "});</script>";
        }
    }
    
    public function beingUsed()
    {
        return count($this->_pies) > 0 || count($this->_charts) > 0;
    }
    
    private function _formatSnapshot($data)
    {
         return is_string($data["curve_snapshot"]) ? json_decode($data["curve_snapshot"]) : $data["curve_snapshot"];
    }
}
<?php

App::uses('CakeSession', 'Model/Datasource');
class ChartHelper extends AppHelper {
    
    var $helpers = array('Html');
    
    private $_pies = array();
    private $_charts = array();
    private $_players = array();
    
    const CURVE_MINIMUM_INDEX = 0;
    const CURVE_AVERAGE_INDEX = 1;
    const CURVE_MAXIMUM_INDEX = 2;
        
    
    public function formatScore($score)
    {
        if(is_null($score))
        {
            return "N/A";
        }
        return $this->formatPct(round($score * 100));
    }

    public function formatPct($score)
    {
        if(!is_null($score))
        {
            $class = "neutral";
            if($score > 80) {
                $class = "positive";
            }
            elseif($score < 60)
            {
                $class = "negative";
            }

            return  sprintf("<span class=\"%s\">%s</span>", $class, $score . "%");
        }
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
        
        return '<div class="enjoyment-chart">
                    <div class="like" style="height:'. ($data["liking_pct"] ? $data["liking_pct"] : 0) .'%;"></div>
                    <div class="neutral" style="height:'. ($data["neutral_pct"] ? $data["neutral_pct"] : 100) .'%;"></div>
                    <div class="dislike" style="height:'. ($data["disliking_pct"] ? $data["neutral_pct"] : 0).'%;"></div>
                </div>';
    }

    public function getHorizontalGraph($type, $key, $data)
    {
        return '<div class="enjoyment-chart horizontal">
                    <div class="average">'. $this->formatScore($data["score_snapshot"]) .'</div>
                    <div class="enjoyment">
                        ' . ($data["liking_pct"] > 0 ? '<div class="like" style="width:'.$data["liking_pct"].'%;""><i class="fa fa-smile-o"></i></div>' : '') . '
                        ' . ($data["neutral_pct"] > 0 ? '<div class="neutral" style="width:'.$data["neutral_pct"].'%;""><i class="fa fa-meh-o"></i></div>' : '') . '
                        ' . ($data["disliking_pct"] > 0 ? '<div class="dislike" style="width:'.$data["disliking_pct"].'%;""><i class="fa fa-frown-o"></i></div>' : '') . '
                    </div>
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
        return '<div class="enjoyment-chart big">
                    <div class="like" style="height:'.$data["liking_pct"].'%;""><i class="fa fa-smile-o"></i></div>
                    <div class="neutral" style="height:'.$data["neutral_pct"].'%;""><i class="fa fa-meh-o"></i></div>
                    <div class="dislike" style="height:'.$data["disliking_pct"].'%;""><i class="fa fa-frown-o"></i></div>
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
        return count($this->_pies) > 0 || count($this->_charts) || count($this->_players) > 0;
    }
    
    private function _formatSnapshot($data)
    {
         return is_string($data["curve_snapshot"]) ? json_decode($data["curve_snapshot"]) : $data["curve_snapshot"];
    }
}
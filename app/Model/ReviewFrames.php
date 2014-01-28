<?php

class ReviewFrames extends AppModel
{	    
	//public $name        = 'ReviewFrame';  
    public $useTable    = "review_frames";
    public $belongsTo   = array("Album", "User", "Track", "Artist");
        
        
    public function savePlayerData($reviewedFrames, $keyMapping)
    {
        $formattedData = array();

        foreach($reviewedFrames as $frame)
        {
            $formattedData[] = array(
                "groove"        => $frame["gv"],
                "grooving"      => $frame["g"],
                "starpowering"  => $frame["st"],
                "suckpowering"  => $frame["su"],
                "multiplier"    => array_key_exists("m", $frame) ? $frame["m"] : 0,
                "position"      => array_key_exists("p", $frame) ? $frame["p"] : 0,
                "last"          => array_key_exists("o", $frame) ? $frame["o"] : null,
                "user_id"       => $keyMapping[3],
                "track_id"      => $keyMapping[2],
                "album_id"      => $keyMapping[1],
                "artist_id"     => $keyMapping[0],
                "review_id"     => $keyMapping[4]
            );
        }

        return $this->saveMany($formattedData);
    }    
    
    public function getTopPositions($condition)
    { 
        return $this->query("
           SELECT MAX(TopAreas.total) AS total, track_id, position
           FROM 
                (   SELECT track_id, (groove + groove * multiplier) AS total, position
                    FROM review_frames
                    WHERE $condition
                    ORDER BY multiplier DESC, groove DESC, starpowering DESC
                ) AS TopAreas 
                GROUP BY track_id
            LIMIT 5
        ");
    }
                
    public function getAppreciation($condition)
    {        
        $data = $this->query("
            SELECT * 
            FROM 
                (SELECT count(*) AS liking_qty FROM review_frames WHERE $condition AND groove > .75) AS t1,
                (SELECT count(*) AS disliking_qty FROM review_frames WHERE $condition AND groove < .25) AS t2,
                (SELECT count(*) AS total_qty FROM review_frames WHERE $condition) AS t3;
        ");
        
        $liking     = $data[0]["t1"]["liking_qty"];
        $disliking  = $data[0]["t2"]["disliking_qty"];        
        $total      = $data[0]["t3"]["total_qty"];
        $neutral    = $total - $disliking - $liking;
        
        // this prevents divisions by 0
        if($total < 1)
        {
            $total = 1;
            $neutral = 1;
        }
        
        return array(
            "liking"        => $liking,
            "disliking"     => $disliking,
            "neutral"       => $neutral,
            "liking_pct"    => $liking      / $total * 100,
            "disliking_pct" => $disliking   / $total * 100,
            "neutral_pct"   => $neutral     / $total * 100,
            "total"         => $total
        );
    }
    
    public function getByCreated($conditions, $limit = 5)
    {   
        return $this->find("all", array(
            "conditions" => $conditions,
            "fields"=> array("Album.*", "Track.*", "Artist.*"),
            "order" => array("ReviewFrames.created DESC"),
            "group" => array("track_id", "album_id", "artist_id"),
            "limit" => $limit
        ));
    }
    
    public function getRawCurve($conditions)
    {        
        return $this->find("all", array(
            "conditions"    => $conditions,
            "fields" => array( 
                "AVG(groove) as avg_groove",
                "(AVG(groove) + AVG(groove) * AVG(multiplier)) as calc_groove",                
               // "AVG(suckpowering) as avg_suckpowering", 
               // "AVG(starpowering) as avg_starpowering",
               // "ReviewFrames.album_id as album_id", 
               // "ReviewFrames.track_id as track_id",
               // "ReviewFrames.position as position"
            ),
            "group" => array("ReviewFrames.album_id", "ReviewFrames.track_id", "position")
        ));
    }
    
    public function getRawCurveByCreated($conditions)
    {
        return $this->find("all", array(
            "conditions"    => $conditions,
            "fields"        => array("ReviewFrames.album_id as album_id", 
                                "ReviewFrames.track_id as track_id", 
                                "AVG(groove) as avg_groove", 
                                "MAX(groove) as max_groove", 
                                "MIN(groove) as min_groove", 
                                "AVG(suckpowering) as avg_suckpowering", 
                                "AVG(starpowering) as avg_starpowering", 
                                "(groove + groove * multiplier) as calc_groove", 
                                "position"),
            "order"         => array("ReviewFrames.created DESC"),
            "group"         => array("ReviewFrames.user_id", "ReviewFrames.review_id", "ReviewFrames.album_id", "ReviewFrames.track_id", "position")
        )); 
    }    
    
    public function mergeAppreciationData($previous, $newone)
    {
        $liking     = (int)$previous["liking"]      + (int)$newone["liking"];
        $disliking  = (int)$previous["disliking"]   + (int)$newone["disliking"];
        $neutral    = (int)$previous["neutral"]     + (int)$newone["neutral"];
        $total      = $liking + $disliking + $neutral;
        
        // this prevents divisions by 0
        if($total < 1)
        {
            $total = 1;
            $neutral = 1;
        }
                
        return array(
            "liking"        => $liking,
            "disliking"     => $disliking,
            "neutral"       => $neutral,
            "liking_pct"    => $liking      / $total * 100,
            "disliking_pct" => $disliking   / $total * 100,
            "neutral_pct"   => $neutral     / $total * 100,
            "total"         => $total
        );
    }        
    
    /**
     * This function expect review frame data that has already been merge by positions.
     * @param type $curveData
     * @param type $positionsPerFrame
     * @param type $resolution
     * @return type
     */
    public function roundReviewFramesSpan($curveData, $positionsPerFrame, $resolution)
    {
        $curve = array_fill(null, $resolution, null);
        $count = count($curveData);
                
        foreach($curve as $idx => $point)
        {
            $skippedFrames = 0;
            $avg = 0;
            $max = 0;
            $min = 0;
            $calc = 0;
            $avgStarpowering = 0;
            $avgSuckpowering = 0;
            
            while($skippedFrames < $positionsPerFrame && $count > $idx)
            {
                //$max                += $curveData[$idx][0]["max_groove"];
               // $min                += $curveData[$idx][0]["min_groove"];
                $avg                += $curveData[$idx][0]["avg_groove"];
                $calc               += $curveData[$idx][0]["calc_groove"];
               // $avgStarpowering    += $curveData[$idx][0]["avg_starpowering"];
               // $avgSuckpowering    += $curveData[$idx][0]["avg_suckpowering"];
                $skippedFrames++;
            }
                       
            // Only save when resolution is large enough for having frame data.
            if($skippedFrames > 0)
            {
                $curve[$idx] = array(                    
                   // "min" => $min                / $skippedFrames,
                    "avg" => $avg                / $skippedFrames,
                   // "max" => $max                / $skippedFrames,
                    "calc" => $calc               / $skippedFrames,
                    //"sp" => $avgStarpowering    / $skippedFrames,
                    //"ss" => $avgSuckpowering    / $skippedFrames
                );
            }
        }        
        
        return $curve;
    }
    
}
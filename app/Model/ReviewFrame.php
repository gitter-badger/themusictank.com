<?php
App::uses('Track', 'Model');
App::uses('UserActivity', 'Model');
class ReviewFrame extends AppModel
{
    public $useTable    = "review_frames";
    public $belongsTo   = array("Album", "User", "Track", "Artist");

    public function savePlayerData($reviewedFrames, $keyMapping)
    {
        $formattedData = array();

        $data = $this->Track->findById($keyMapping[2]);
        if(!$data)
        {
            throw new NotFoundException('Could not find that track');
        }

        foreach($reviewedFrames as $frame)
        {
            $formattedData[] = array(
                "groove"        => $frame["gv"],
                "starpowering"  => $frame["st"],
                "suckpowering"  => $frame["su"],
                "multiplier"    => array_key_exists("m", $frame) ? $frame["m"] : 0,
                "position"      => array_key_exists("p", $frame) ? $frame["p"] : 0,
                "artist_id"     => $keyMapping[0],
                "album_id"      => $keyMapping[1],
                "track_id"      => $keyMapping[2],
                "user_id"       => $keyMapping[3],
                "review_id"     => $keyMapping[4]
            );
        }

        $last = array_pop($reviewedFrames);
        if((int)$last["o"] > 0)
        {
            $activity = new UserActivity();
            $activity->add($keyMapping[3], UserActivity::TYPE_REVIEW_COMPLETE, $keyMapping[2]);

            $track = new Track();
            $track->data = array(
                "User" => array("id" => $keyMapping[3]),
                "Track" => array("id" => $keyMapping[2]),
                "Album" => array("id" => $keyMapping[1])
            );
            $track->onReviewComplete();
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
                "AVG(suckpowering) as avg_suckpowering",
                "AVG(starpowering) as avg_starpowering",
                "ReviewFrames.track_id as track_id"
            ),
            "group" => array("ReviewFrames.album_id", "ReviewFrames.track_id", "ReviewFrames.position")
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
    public static function lowerSpanResolution($curveData, $positionsPerFrame, $resolution)
    {
        if(count($curveData) > 0 && count($curveData) < $resolution)
        {
            $positionsPerFrame = 1;
            $length = count($curveData);
        }
        else
        {
            $length = $resolution;
        }

        $curve = array_fill(0, $length, null);
        $count = count($curveData);

        foreach($curve as $idx => $point)
        {
            $skippedFrames = 0;
            $avg = 0;
            $calc = 0;

            while($skippedFrames < $positionsPerFrame && $count > $idx)
            {
                $avg                += $curveData[$idx][0]["avg_groove"];
                $calc               += $curveData[$idx][0]["calc_groove"];
                $skippedFrames++;
            }

            // Only save when resolution is large enough for having frame data.
            if($skippedFrames > 0)
            {
                $curve[$idx] = array(
                    "avg" => round($avg  / $skippedFrames, 3),
                    "calc" => round($calc / $skippedFrames, 3)
                );
            }
        }

        return $curve;
    }

    public static function getTrackSpan($reviewFrames, $trackId)
    {
        $startIdx = null;
        $count = 0;
        foreach($reviewFrames as $idx => $frame)
        {
            if(is_null($startIdx) && $frame["ReviewFrames"]["track_id"] === $trackId)
            {
                $startIdx = $idx;
            }

            if($startIdx >= 0 && $frame["ReviewFrames"]["track_id"] === $trackId)
            {
                $count++;
            }
        }

        return ($count > 0) ?
            array_slice($reviewFrames, $startIdx, $count) :
            array();
    }

     /** The final number of frames is the resolution's value.
     * Compare to the length in order to sum values that
     * have to be merged to fit the curve's resolution
     * @param integer $duration
     * @param double $resolution
     * @return integer
     */
    public static function resolutionToPositionsPerFrames($duration, $resolution)
    {
        return ($duration > $resolution) ? ($duration  / $resolution) : $duration;
    }

}

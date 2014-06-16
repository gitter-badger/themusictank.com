<?php
/**
 * TableSnapshot is a class that handles common functions for models needing
 * to save ReviewFrames snapshots.
 */
App::uses('ReviewFrames', 'Model');

class TableSnapshot extends AppModel
{
	private $_jsondFields = array("curve", "ranges", "top", "bottom");

    public function afterFind($results, $primary = false)
    {
        if(!array_key_exists("id", $results))
        {
            foreach($results as $idx => $row)
            {
                if(array_key_exists($this->alias, $row))
                {
                	foreach ($this->_jsondFields as $field)
                	{
	                    if(array_key_exists($field, $row[$this->alias]) && is_string($row[$this->alias][$field]))
	                    {
	                        $results[$idx][$this->alias][$field] = json_decode($row[$this->alias][$field]);
	                    }
                	}
                }
            }
        }
        return $results;
    }

    public function beforeSave($options = array())
    {
		foreach ($this->_jsondFields as $field)
    	{
            if(array_key_exists($field, $this->data[$this->alias]) && !is_string($this->data[$this->alias][$field]))
	        {
	            $this->data[$this->alias][$field] = json_encode($this->data[$this->alias][$field]);
	        }
    	}

        return true;
    }

	public function getExpiredRange()
	{
		return time() - (HOUR * 12);
	}


    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function updateCached($conditions)
    {
        if($this->requiresUpdate())
        {
            return $this->snap($conditions);
        }

        return Hash::extract($this->data, $this->alias);
    }

    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function snap($conditions)
    {
    	return $this->_createSnapshot($conditions);
    }

    /**
     * Returns the whether or not the cached snapshot is still valid
     * @return boolean True if outdated, false if still ok
     */
    public function requiresUpdate()
    {
    	$timestamp = (int)Hash::get($this->data, $this->alias . ".lastsync");
        return $timestamp < $this->getExpiredRange();
    }

    public function getUserIdsWhoReviewedTrack($trackId)
    {
    	$frames = new ReviewFrames();
    	return Hash::extract($frames->find("all", array(
	    		"conditions" => array("ReviewFrames.track_id" => $trackId),
	    		"fields" => array("DISTINCT user_id")
			)),
			"{n}.ReviewFrames.user_id");
    }

	public function filterUserIdsWhoReviewedTrack($trackId, $userIds) {

    	$frames = new ReviewFrames();
    	return $frames->find("all", array(
    		"conditions" => array(
    			"ReviewFrames.track_id" => $trackId,
    			"ReviewFrames.user_id" => $userIds
			),
    		"fields" => array("DISTINCT user_id"),
    		"limit" => 100
		));
	}

    public function getUserIdsWhoReviewedAblum($albumId)
    {
    	$frames = new ReviewFrames();
    	return Hash::extract($frames->find("all", array(
	    		"conditions" => array("ReviewFrames.album_id" => $albumId),
	    		"fields" => array("DISTINCT user_id")
			)),
			"{n}.ReviewFrames.user_id");
    }

	public function filterUserIdsWhoReviewedAblum($albumId, $userIds) {
    	$frames = new ReviewFrames();
    	return $frames->find("all", array(
	    		"conditions" => array(
	    			"ReviewFrames.album_id" => $albumId,
	    			"ReviewFrames.user_id" => $userIds
    			),
	    		"fields" => array("DISTINCT user_id"),
	    		"limit" => 100
			));
	}



    /**
     * Returns all the reviewing data based on a Model query. This function permits user based queries.
     * @param array $conditions A series of conditions
     * @return array Matching review frames dataset
     */
    public function getRawCurveSpan($conditions)
    {
        $rf = new ReviewFrames();
        return $rf->getRawCurveByCreated($conditions);
    }

    /**
     * Return the most recent reviews frames matching the conditions
     * @param type $conditions A series of conditions
     * @param type $limit Limits the number of results. Default = 5.
     * @return array Matching review frames dataset
     */
    public function getRecentReviews($conditions, $limit = 5)
    {
        $rf = new ReviewFrames();
        return $rf->getByCreated($conditions, $limit);
    }

    /**
     * Returns the max, min and average values of all linked model reviews
     * @param integer $belongsToId The id of the object we are pulling data from
     * @param integer $timestamp The earliest timestamp of the span. Defaults to 0.
     * @return boolean True on success, False on failure
     */
    public function getAppreciation($conditions)
    {
		$rf = new ReviewFrames();
        return $rf->getAppreciation($this->_conditionArrayToString($conditions));
    }

    protected function _conditionArrayToString($conditions)
    {
		$conditionsStr = array();
        foreach ($conditions as $key => $value) {
        	if(is_array($value)) {
        		$conditionsStr[] = $key . " IN (" . implode(",", array_merge(array(0), $value)) . ") ";
        	}
        	else {
        		$conditionsStr[] = $key . "=" . $value . " ";
        	}
        }
        return implode(" AND ", $conditionsStr);
    }

    /**
     * Based on all the data, compiles the general score of the object
     * @param array $curveData A dataset of review frames
     * @return float The average scorein percent
     */
    public function compileScore($curveData)
    {
        $total = 0;
        $count = count($curveData);

        if($count > 0)
        {
            foreach($curveData as $data)
            {
                $total += $data[0]["avg_groove"];
                //$total += $data[0]["calc_groove"];
            }

            return $total / count($curveData);
        }

        return null;
    }

    /**
     * Returns the max, min and average values of all linked model reviews based on a user
     * @param integer $belongsToId The id of the object we are pulling data from
     * @param integer $userId A user id.
     * @param integer $timestamp The earliest timestamp of the span. Defaults to 0.
     * @return boolean True on success, False on failure
     */
    public function getUserAppreciation($belongsToId, $userId, $timestamp = 0)
    {
        $belongsToAlias = strtolower($this->getBelongsToAlias() . "_id");
        $rf = new ReviewFrames();
        return $rf->getAppreciation("user_id = $userId AND $belongsToAlias = $belongsToId AND created > $timestamp");
    }


    public function getSpan($start, $end, $limit = 100)
    {
        return $this->find("all", array(
            "conditions" => array(
                "lastsync >=" => $start,
                "lastsync <=" => $end
            ),
            "order" => array("score_snapshot DESC", "liking DESC", "total DESC"),
            "limit" => $limit,
            "recursive" => false
        ));
    }


    /**
     * Returns the current values of Model->data for the belongs to Model.
     * @return array The preloaded data of the object
     */
    public function getBelongsToData()
    {
        return Hash::get($this->data, $this->getBelongsToAlias());
    }

    /**
     * Returns pre-populated extra fields that need to be saved along
     * side each snapshots.
     * @return array Data ready to be saved
     */
    public function getExtraSaveFields($conditions = array())
    {
        $extras = array();
        $extras["lastsync"]  = time();
		$extras["id"] = null;

        $id = (int)Hash::get($this->data, $this->alias . ".id");
        if($id > 0)
        {
           $extras["id"] = $id;
        }

        return $extras;
    }


    /**
     * Creates a model's snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    protected function _createSnapshot($conditions)
    {
        $avgs 		= $this->getAppreciation($conditions);
        $curve 		= $this->getAverageCurve($conditions);
        $score 		= $this->getAverageScore($conditions);
        $ranges 	= $this->getRangeAverages($conditions, $curve);

        $topArea 	= $this->getTopAreaCurve($conditions);
        $bottomArea = $this->getBottomAreaCurve($conditions);

        $saveArray = array_merge(
        	$this->getExtraSaveFields($conditions),
        	$avgs,
        	array(
    			"score" => $score,
        		"curve" => $curve,
        		"ranges" => $ranges,
        		"top" 	=> $topArea,
        		"bottom" => $bottomArea
    		)
    	);

		return $this->save($saveArray) ? $saveArray : null;
    }


    public function getCurveAverage($curve)
    {
    	$total = 0;
    	foreach ($curve as $value) {
    		$total += (float)$value["avg"];
    	}

        if (count($curve) > 0) {
            return $total / count($curve);
        }
    }

    public function getAverageCurve($conditions)
    {
    	$review = new ReviewFrames();
    	$records = $review->find("all", array(
            "conditions"    => $conditions,
            "fields"        => array("AVG(groove) as avg", "position"),
            "order"         => array("ReviewFrames.position ASC"),
            "group"         => array("ReviewFrames.position")
        ));
        return Hash::extract($records, '{n}.{n}');
    }

    public function getTopAreaCurve($conditions)
    {
    	$review = new ReviewFrames();
    	$records = $review->getTopArea($this->_conditionArrayToString($conditions));
        return Hash::extract($records, '{n}.{n}');
    }

    public function getBottomAreaCurve($conditions)
    {
    	$review = new ReviewFrames();
    	$records = $review->getBottomArea($this->_conditionArrayToString($conditions));
        return Hash::extract($records, '{n}.{n}');
    }

    public function getAverageScore($conditions)
    {
    	$review = new ReviewFrames();
    	$records = $review->find("all", array(
            "conditions"    => $conditions,
            "fields"        => array("AVG(groove) as avg")
        ));

        return Hash::get($records, '{n}.{n}.avg');
    }

    public function getRangeAverages($conditions, $curve)
    {
    	$review = new ReviewFrames();
    	$overallAverage = $this->getCurveAverage($curve);

		$averageHighs = $review->find("all", array(
            "conditions"    => array_merge($conditions, array("ReviewFrames.groove >" => $overallAverage)),
            "fields"        => array("AVG(groove) as avg", "position"),
            "order"         => array("ReviewFrames.position ASC"),
            "group"         => array("ReviewFrames.position")
        ));
		$averageLows = $review->find("all", array(
            "conditions"    => array_merge($conditions, array("ReviewFrames.groove <" => $overallAverage)),
            "fields"        => array("AVG(groove) as avg", "position"),
            "order"         => array("ReviewFrames.position ASC"),
            "group"         => array("ReviewFrames.position")
        ));

        $data = array();
        foreach($curve as $default) {
        	$data[] = array(
        		"min" => (float)$default["avg"],
        		"max" => (float)$default["avg"]
    		);
        }
        foreach ($averageHighs as $high) {
        	$data[ Hash::get($high, "ReviewFrames.position") ]["max"] = (float)$high[0]["avg"];
        }
        foreach ($averageLows as $low) {
        	$data[ Hash::get($low, "ReviewFrames.position") ]["min"] = (float)$low[0]["avg"];
        }

        return $data;
    }

    /* *
     * Validates and saves a snapshot
     * @private
     * @param array $appreciation
     * @param array $curve
     * @return boolean True on success, false on failure

    private function _validateAndSave($conditions, $avgs, $curve, $ranges)
    {
    	/ *
        $saveArray = array_merge($appreciation, $this->getExtraSaveFields(), $this->_validate($appreciation, $curve));
        return $this->save($saveArray) ? $saveArray : null; * /

        $saveArray = array_merge(
        	$this->getExtraSaveFields($conditions),
        	$avgs,
        	array(
        		"curve" => $curve,
        		"ranges" => $ranges
    		)
    	);

		return $this->save($saveArray) ? $saveArray : null;
    }*/


    /**
     * Gets the model linked to the snapshot through the belongs to association.
     * Expects that the object only belongs to one parent object.
     * @return string The name of the object.
	*/
    public function getBelongsToAlias()
    {
        $belongsAliases = array_keys($this->belongsTo);
        return $belongsAliases[0];
    }
}

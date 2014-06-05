<?php
/**
 * TableSnapshot is a class that handles common functions for models needing
 * to save ReviewFrames snapshots.
 */

App::uses('ReviewFrames', 'Model');

class TableSnapshot extends AppModel
{
    public function afterFind($results, $primary = false)
    {
        if(!array_key_exists("id", $results))
        {
            foreach($results as $idx => $row)
            {
                if(array_key_exists($this->alias, $row))
                {
                    if(array_key_exists("curve", $row[$this->alias]) && is_string($row[$this->alias]["curve"]))
                    {
                        $results[$idx][$this->alias]["curve"] = json_decode($row[$this->alias]["curve"]);
                    }
                    if(array_key_exists("ranges", $row[$this->alias]) && is_string($row[$this->alias]["ranges"]))
                    {
                        $results[$idx][$this->alias]["ranges"] = json_decode($row[$this->alias]["ranges"]);
                    }
                }
            }
        }
        return $results;
    }

    public function beforeSave($options = array())
    {
		if(array_key_exists("curve", $this->data[$this->alias]) && !is_string($this->data[$this->alias]["curve"]))
        {
            $this->data[$this->alias]["curve"] = json_encode($this->data[$this->alias]["curve"]);
        }
        if(array_key_exists("ranges", $this->data[$this->alias]) && !is_string($this->data[$this->alias]["ranges"]))
        {
            $this->data[$this->alias]["ranges"] = json_encode($this->data[$this->alias]["ranges"]);
        }

        return true;
    }

    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function updateCached($conditions)
    {
        $this->data[$this->alias] = Hash::extract($this->find("first", $conditions), $this->alias);

        if($this->requiresUpdate())
        {
            $this->snap($conditions);
            $this->data[$this->alias] = Hash::extract($this->find("first", $conditions), $this->alias);
        }

        return $this->data;
    }

    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function snap($conditions)
    {
    	return $this->_createSnapshot($conditions);
/*

        $id = $this->getData($this->alias . ".id");
        return (empty($id)) ? $this->_createSnapshot() : $this->_updateSnapshot(); */
    }

    /**
     * Returns the whether or not the cached snapshot is still valid
     * @return boolean True if outdated, false if still ok
     */
    public function requiresUpdate()
    {
    	$timestamp = Hash::get($this->data, $this->alias . ".lastsync");
        return (int)$timestamp + (HOUR*12) < time();

        $timestamp = $this->getData($this->alias . ".lastsync");
        return empty($timestamp) || $timestamp + (HOUR*12) < time();
    }

/*
    public function getUserIdsWhoReviewed($trackId, $userIdFilter = null)
    {
        $rf = new ReviewFrames();
        $limit = null;
        $conditions = array('track_id'  => $trackId);

        if(is_null($userIdFilter))
        {
            $limit = 5;
        }
        else
        {
            $conditions['user_id'] = $userIdFilter;
        }

        return $rf->find("list", array(
            'conditions' => $conditions,
            "limit" => $limit,
            "fields" => "user_id",
            "group" => "user_id",
            'order' => 'rand()'
            )
        );
    }*/

    public function getUserIdsWhoReviewedTrack($trackId)
    {
    	$frames = new ReviewFrames();
		//$count = $frames->find("count", array("conditions" => array("ReviewFrame.album_id" => $albumId), "fields" => "DISTINCT user_id"));

    	return Hash::extract($frames->find("all", array(
	    		"conditions" => array("ReviewFrames.track_id" => $trackId),
	    		"fields" => array("DISTINCT user_id")
			)),
			"n.ReviewFrames.user_id");
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
		//$count = $frames->find("count", array("conditions" => array("ReviewFrame.album_id" => $albumId), "fields" => "DISTINCT user_id"));

    	return Hash::extract($frames->find("all", array(
	    		"conditions" => array("ReviewFrames.album_id" => $albumId),
	    		"fields" => array("DISTINCT user_id")
			)),
			"n.ReviewFrames.user_id");
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
     * Returns all the reviewing data based on a Model query. This function does not permit user based queries.
     * @param int $timestamp The starting timestamp for all searches. Defaut 0
     * @return array Matching review frames dataset
     */
    public function getRawCurveData($timestamp = 0, $extraConditions = array())
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToLabel = strtolower($this->getBelongsToAlias()) . "_id";
        $belongsToId    = (int)$belongsToData["id"];
        $rf             = new ReviewFrames();

        $conditions = array_merge($extraConditions, array(
            "ReviewFrames.$belongsToLabel"  => $belongsToId,
            "ReviewFrames.created >"        => $timestamp
        ));

        return $rf->getRawCurve($conditions);
    }

    public function getRawSplitData($threshold, $timestamp = 0, $extraConditions = array())
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToLabel = strtolower($this->getBelongsToAlias()) . "_id";
        $belongsToId    = (int)$belongsToData["id"];
        $reviews        = new ReviewFrames();

        $conditions = array_merge($extraConditions, array(
            "ReviewFrames.$belongsToLabel"  => $belongsToId,
            "ReviewFrames.created >"        => $timestamp
        ));


        $avgMax = $reviews->getRawCurve(array_merge($conditions, array("ReviewFrames.groove >" => $threshold)));
        $avgMin = $reviews->getRawCurve(array_merge($conditions, array("ReviewFrames.groove <" => $threshold)));

        return array("min" => $avgMin, "max" => $avgMax);
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
   // public function getAppreciation($belongsToId, $timestamp = 0, $extraConditions = null)
    public function getAppreciation($conditions)
    {
    	/*
        $belongsToAlias = strtolower($this->getBelongsToAlias() . "_id");
        $conditions = "$belongsToAlias = $belongsToId AND created > $timestamp";
        $rf = new ReviewFrames();

        if(!is_null($extraConditions))
        {
            $conditions .= " AND $extraConditions";
        } */

        return array();


		$rf = new ReviewFrames();
        $conditionsStr = "";


        foreach ($conditions as $key => $value) {
        	foreach ($value as $key2 => $value2) {
	        	$conditionsStr .= strtolower($key . "_" . $key2) . " = " . $value2 . " ";
	        }
        }

        return $rf->getAppreciation($conditionsStr);
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
    public function getExtraSaveFields()
    {
        $extras = array();

        $belongsToAlias = $this->getBelongsToAlias();
        $belongsToData  = $this->getBelongsToData();

        $extras["lastsync"]  = time();
        $extras[strtolower($belongsToAlias) . "_id"] = (int)$belongsToData["id"];

        $id = (int)Hash::check($this->data, $this->alias . ".id");
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
        $ranges 	= $this->getRangeAverages($conditions, $curve);

        return $this->_validateAndSave($avgs, $curve, $ranges);
 /*
        $appreciation   = $this->getAppreciation($belongsToId);
        $curve          = $this->getCurve($belongsToId, 150);

        return $this->_validateAndSave($appreciation, $curve); */
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

/*
    public function temporarySnapshot()
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToId    = (int)$belongsToData["id"];

        $appreciation   = $this->getAppreciation($belongsToId);
        $curve          = $this->getCurve($belongsToId, 150);


        $saveArray = array_merge($appreciation, $this->getExtraSaveFields());
        if(array_key_exists("ppf", $curve))
        {
            $saveArray["snapshot_ppf"]      = $curve["ppf"];
        }

        if(array_key_exists("curve", $curve))
        {
            $saveArray["curve_snapshot"]    = $curve["curve"];
        }

        if(array_key_exists("score", $curve))
        {
            $saveArray["score_snapshot"]    = $curve["score"];
        }

        if(array_key_exists("split", $curve))
        {
            $saveArray["range_snapshot"]    = $curve["split"];
        }

        return $saveArray;
    }*/


    /* *
     * Updates an existing model snapshot
     * @private
     * @return boolean True on success, false on failure

    private function _updateSnapshot()
    {
        $belongsToData  = $this->getBelongsToData();
        $belongsToId    = (int)$belongsToData["id"];
        $timestamp      = $this->getData($this->alias . ".lastsync");
        $rf             = new ReviewFrames();

        $appreciation   = $rf->mergeAppreciationData($this->data[$this->alias], $this->getAppreciation($belongsToId, $timestamp));
        $curve          = $this->getCurve($belongsToId, 150, $timestamp);

        return $this->_validateAndSave($appreciation, $curve);
    }*/

    /**
     * Validates and saves a snapshot
     * @private
     * @param array $appreciation
     * @param array $curve
     * @return boolean True on success, false on failure
     */
    private function _validateAndSave($avgs, $curve, $ranges)
    {
    	/*
        $saveArray = array_merge($appreciation, $this->getExtraSaveFields(), $this->_validate($appreciation, $curve));
        return $this->save($saveArray) ? $saveArray : null;*/

        $saveArray = array_merge(
        	$this->getExtraSaveFields(),
        	$avgs,
        	array(
        		"curve" => $curve,
        		"ranges" => $ranges
    		)
    	);

		return $this->save($saveArray) ? $saveArray : null;
    }

/*
    private function _validate($appreciation, $curve)
    {
        if(array_key_exists("ppf", $curve))
        {
            $saveArray["snapshot_ppf"]      = $curve["ppf"];
        }

        if(array_key_exists("curve", $curve))
        {
            $saveArray["curve_snapshot"]    = $curve["curve"];
        }

        if(array_key_exists("score", $curve))
        {
            $saveArray["score_snapshot"]    = $curve["score"];
        }

        if(array_key_exists("split", $curve))
        {
            $saveArray["range_snapshot"]    = $curve["split"];
        }

        return $saveArray;
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

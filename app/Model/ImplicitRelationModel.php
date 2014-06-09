<?php

/**
* Objects that save data which is related to another object is considered a ImplicitRelationModel.
* for exemple, a useactivity of liking an album. "Album" is load through an implicit relation.
*/
class ImplicitRelationModel extends AppModel
{
    // Cache objects that have already been
    // autoloaded to save juice.
    private $_preloadedObjects = array(
        "Achievement" 	=> array(),
        "User" 			=> array(),
        "Track" 		=> array(),
        "Album" 		=> array(),
        "Artist" 		=> array()
    );

    const TYPE_ACHIEVEMENT      = "achievement";
    const TYPE_FOLLOWER         = "follower";
    const TYPE_NEW_ACCOUNT      = "newaccount";
    const TYPE_CREATED_ARTIST   = "newartist";
    const TYPE_REVIEW_COMPLETE  = "reviewcomplete";

    public function getType($typeId)
    {
        return self::$typeId;
    }

    // In a perfect world, this function should be an afterFind
    // but loading users starts a recursive loop.
    public function associateRelated($results)
    {
        foreach($results as $idx => $row)
        {
            if(!empty($row[$this->alias]["related_model_id"]))
            {
                switch(strtolower($row[$this->alias]["type"]))
                {
                    case self::TYPE_ACHIEVEMENT :
                        if(!array_key_exists("Achievement", $results[$idx][$this->alias]))
                        {
                            $results[$idx][$this->alias]["Achievement"] = $this->_loadLinkedAchievement((int)$row[$this->alias]["related_model_id"]);
                        }
                        break;

                    case self::TYPE_FOLLOWER :
                        if(!array_key_exists("UserFollower", $results[$idx][$this->alias]))
                        {
                            $results[$idx][$this->alias]["UserFollower"] = $this->_loadLinkedObject("User", (int)$row[$this->alias]["related_model_id"]);
                        }
                        break;

                    case self::TYPE_REVIEW_COMPLETE :
                        if(!array_key_exists("ReviewedTrack", $results[$idx][$this->alias]))
                        {
                            $results[$idx][$this->alias]["ReviewedTrack"] = $this->_loadLinkedObject("Track", (int)$row[$this->alias]["related_model_id"]);
                            $results[$idx][$this->alias]["ReviewedTrackAlbum"] = $this->_loadLinkedObject("Album", (int)$results[$idx][$this->alias]["ReviewedTrack"]["album_id"]);
                            $results[$idx][$this->alias]["ReviewedTrackArtist"] = $this->_loadLinkedObject("Artist", (int)$results[$idx][$this->alias]["ReviewedTrackAlbum"]["artist_id"]);
                        }
                        break;
                }
            }
        }

        return $results;
    }

    private function _isCached($type, $key)
    {
        $key = "_" . $key;
        return array_key_exists($key, $this->_preloadedObjects[$type]) && !is_null($this->_preloadedObjects[$type][$key]);
    }

    private function _getCached($type, $key)
    {
        $key = "_" . $key;
        return $this->_preloadedObjects[$type][$key];
    }

    private function _saveToCache($type, $key, $obj)
    {
        $key = "_" . $key;
        $this->_preloadedObjects[$type][$key] = $obj;
    }

    private function _loadLinkedAchievement($achievementId)
    {
        if(!$this->_isCached("Achievement", $achievementId))
        {
            $achievement = ClassRegistry::init('Achievement')->getById($achievementId);
            $this->_saveToCache("Achievement", $achievementId, $achievement["Achievement"]);
        }

        return $this->_getCached("Achievement", $achievementId);
    }

    private function _loadLinkedObject($type, $id)
    {
        if(!$this->_isCached($type, $id))
        {
            $obj = ClassRegistry::init($type)->find("first", array(
                "conditions" => array("$type.id" => $id),
                "fields" => "$type.*"
            ));

            $this->_saveToCache($type, $id, $obj[$type]);
        }

        return $this->_getCached($type, $id);
    }
}

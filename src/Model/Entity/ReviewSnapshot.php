<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class ReviewSnapshot extends Entity
{
    public function isNotAvailable()
    {
        return (int)$this->total === 1 && (int)$this->neutral === 1;
    }

    public function hasScore()
    {
        return !is_null($this->score) && $this->total > 1;
    }

    static function getExpiredRange()
    {
        return time() - (HOUR * 12);
    }

    /**
     * Returns the whether or not the cached snapshot is still valid
     * @return boolean True if outdated, false if still ok
     */
    public function requiresUpdate()
    {
        return (int)$this->modified < self::getExpiredRange();
    }


    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function updateCache($conditions)
    {
        if($this->requiresUpdate())
        {
            return $this->snap($conditions);
        }
        return false;
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
     * Returns the max, min and average values of all linked model reviews
     */
    public function getAppreciation($conditions)
    {
        return TableRegistry::get('review_frames')->getAppreciation($conditions);
    }


    /**
     * Creates a model's snapshot
     * @private
     * @return boolean True on success, false on failure
     */
    protected function _createSnapshot($conditions)
    {
        $avgs       = $this->getAppreciation($conditions);
        $curve      = $this->getAverageCurve($conditions);
        $score      = $this->getAverageScore($conditions);
        $ranges     = $this->getRangeAverages($conditions, $curve);
        $topArea    = $this->getTopAreaCurve($conditions);
        $bottomArea = $this->getBottomAreaCurve($conditions);

        $saveArray = array_merge(
            $this->getExtraSaveFields($conditions),
            $avgs,
            [
                "score" => $score,
                "curve" => $curve,
                "ranges" => $ranges,
                "top"   => $topArea,
                "bottom" => $bottomArea
            ]
        );

        return $this->save($saveArray) ? $saveArray : null;
    }

}

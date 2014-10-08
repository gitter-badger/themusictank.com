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
    public function updateCache()
    {
        if($this->requiresUpdate())
        {
            return $this->snap();
        }
        return false;
    }

    public function fetch()
    {
        return $this->updateCache();
    }

    /**
     * Creates or updates a model's snapshot
     * @return boolean True on success, false on failure
     */
    public function snap()
    {
        // Updates totals
        $this->updateTotal();
        $this->updateLikes();
        $this->updateDislikes();
        $this->updateNeutrals();

        // Update curve data
        $this->updateCurve();
        $this->updateHighsRange();
        $this->updateLowsRange();

        // Update the high and low points
        $this->updateTopAreaCurve();
        $this->updateLowAreaCurve();
    }

    public function customizeQuery($query)
    {
        return $query;
    }

    /**
    *
    */
    public function updateTotal()
    {
        // This assumes inheriting object send in a query
        // with the correct conditions.
        $data = $this->customizeQuery(TableRegistry::get('review_frames')->getTotal())->first();

        // 1 prevents divisions by zero
        $this->total = 1;

        if (count($data)) {
            $quantity = (int)$data->quantity;
            $this->total = $quantity > 0 ? $quantity  : 1;
        }
    }

    /**
    *
    */
    public function updateLikes()
    {
        // This assumes inheriting object send in a query
        // with the correct conditions.
        $likes = $this->customizeQuery(TableRegistry::get('review_frames')->getLikes())->first();

        $this->liking = 0;
        if (count($likes)) {
            $this->liking = (int)$likes->quantity;
            $this->liking_pct = $this->liking / $this->total;
        }
    }

    /**
    *
    */
    public function updateDislikes()
    {
        // This assumes inheriting object send in a query
        // with the correct conditions.
        $dislikes = $this->customizeQuery(TableRegistry::get('review_frames')->getDislikes())->first();

        $this->disliking = 0;
        if (count($dislikes)) {
            $this->disliking = (int)$dislikes->quantity;
            $this->disliking_pct = $this->disliking / $this->total;
        }
    }

    public function updateNeutrals()
    {
        $this->neutral = $this->total - $this->disliking - $this->liking;
        $this->neutral_pct = $this->neutral / $this->total;
    }

    public function updateCurve()
    {
        $dataset = $this->customizeQuery(TableRegistry::get('review_frames')->getAverageCurve())->all();

        // Flatten the results
        $this->curve = [];
        $values = 0;

        foreach($dataset as $curveByPosition) {
            $values += $curveByPosition->avg_curve;
            $this->curve[(int)$curveByPosition->position] = (float)$curveByPosition->avg_groove;
        }

        $this->score = count($dataset) ? $values / count($dataset) : 0.5;
    }

    public function updateHighsRange()
    {
        $query = $this->customizeQuery(TableRegistry::get('review_frames')->getAverageCurve());
        $query->where(['groove > ' => $this->score]);
        $dataset = $query->all();

        // Copy the default curve to ensure we have
        // data on each frame.
        $this->highs = $this->curve;

        foreach($dataset as $row) {
            $this->highs[$row->position] = (float)$row->avg_groove;
        }
    }

    public function updateLowsRange()
    {
        $query = $this->customizeQuery(TableRegistry::get('review_frames')->getAverageCurve());
        $query->where(['groove < ' => $this->score]);
        $dataset = $query->all();

        // Copy the default curve to ensure we have
        // data on each frame.
        $this->lows = $this->curve;

        foreach($dataset as $row) {
            $this->lows[$row->position] = (float)$row->avg_groove;
        }
    }

    public function updateTopAreaCurve()
    {
        $nbSeconds = 30;
        $query = $this->customizeQuery(TableRegistry::get('review_frames')->getCurveScan($nbSeconds));
        $dataset = $query->order(['avg_groove' => 'DESC'])->first();

        $start = !is_null($dataset) ? (int)$dataset->area : 0;
        $this->top = [$start, $start + $nbSeconds];
    }

    public function updateLowAreaCurve()
    {
        $nbSeconds = 30;
        $query = $this->customizeQuery(TableRegistry::get('review_frames')->getCurveScan($nbSeconds));
        $dataset = $query->order(['avg_groove' => 'ASC'])->first();

        $end = !is_null($dataset) ? (int)$dataset->area : 0;
        $this->bottom = [$end, $end + $nbSeconds];
    }
}

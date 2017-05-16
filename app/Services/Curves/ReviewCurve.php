<?php

namespace App\Services\Curves;

use App\Models\ReviewFrame;

class ReviewCurve extends CachelessCurvesService
{
    public function filterFrames()
    {
        $query = ReviewFrame::whereTrackId($this->track->id);

        if (!is_null($this->user)) {
            $query->whereUserId($this->user->id);
        }

        return $query->get();
    }
}

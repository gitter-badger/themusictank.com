<?php

namespace App\Services\Curves;

use App\Models\TrackReview;

class SubscriptionsCurve extends CachelessCurvesService
{
    public function filterFrames()
    {
        return TrackReview::forTrack($this->track)
            ->whereIn('user_id', $this->subscriptions())
            ->get();
    }

    protected function subscriptions()
    {
        return $this->user->subscriptions->pluck("sub_id");
    }
}

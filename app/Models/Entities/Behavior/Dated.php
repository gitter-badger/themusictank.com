<?php

namespace App\Models\Entities\Behavior;

use Carbon\Carbon;

trait Dated
{
    public function getLastUpdatedForHumans()
    {
        return Carbon::parse($this->last_updated)->diffForHumans();
    }
}

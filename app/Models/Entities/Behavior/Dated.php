<?php

namespace App\Models\Entities\Behavior;

use Carbon\Carbon;

trait Dated
{
    public function getLastUpdatedForHumans()
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    public function getCreatedDateForHumans()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function getDateCreated()
    {
        return $this->created_at;
    }

    public function getDateUpdated()
    {
        return $this->updated_at;
    }
}

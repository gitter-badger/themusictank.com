<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Entities\Behavior\Dated,
        Traits\Sluggable,
        Traits\Searchable;

    public function trackReviews()
    {
        return $this->hasMany(\App\Models\TrackReview::class);
    }
}

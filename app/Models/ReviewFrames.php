<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Models\Traits\Upvotable;

class ReviewFrames extends Model
{
    public $belongsTo = [
        "track" => \App\Models\Entities\Track::class,
        "profile" => \App\Models\Entities\Profile::class
    ];

    public function savePartial($package, \App\Models\Entities\Track $track, \App\Models\Entities\Profile $profile)
    {
        foreach ((array)$package as $idx => $pack) {
            $package[$idx]["profileId"] = $profile->id;
            $package[$idx]["trackId"] = $track->id;
        }

        return $this->post("reviewFrames", ["json" => $package]);
    }
}

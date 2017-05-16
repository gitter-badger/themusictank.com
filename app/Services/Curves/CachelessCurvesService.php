<?php

namespace App\Services\Curves;

use App\Services\GrooveAnalysisService;
use App\Models\Track;
use App\Models\User;

class CachelessCurvesService
{
    protected $track;
    protected $user;

    public function __construct(Track $track, User $user = null)
    {
        $this->track = $track;
        $this->user = is_null($user) ? null : $user;
    }

    public function calculate()
    {
        $groove = new GrooveAnalysisService($this->filterFrames()->toArray());
        return collect($groove->calculate());
    }
}

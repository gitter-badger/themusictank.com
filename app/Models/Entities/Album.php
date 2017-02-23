<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Thumbnailed;
use App\Models\Entities\Behavior\Dated;

class Album
{
    use Thumbnailed,
        Dated;
}

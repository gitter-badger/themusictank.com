<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Dated;

class Track
{
    use Dated;

    public $length;
    public $position;
    public $name;
    public $gid;
    public $youtube_key;
    public $slug;
    public $updated_at;
    public $id;
}

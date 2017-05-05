<?php

namespace App\Models\Traits;

trait Searchable
{
    public static function seach($query)
    {
        return $this->where("name", "regexp", '^'.$query.'/i');
    }
}

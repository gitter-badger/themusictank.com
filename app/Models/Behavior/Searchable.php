<?php

namespace App\Models\Behavior;

trait Searchable
{
    protected $searchColumn = "name";

    public function scopeSearch($query, $criteria)
    {
        return $query->where($this->searchColumn, 'LIKE', '%'.$criteria.'%');
    }
}

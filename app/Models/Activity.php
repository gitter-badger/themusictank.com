<?php

namespace App\Models;

use App\Services\ActivityService;

class Activity extends AppModel
{
    protected $fillable = [
        'user_id',
        'associated_object_id',
        'associated_object_type',
        'must_notify',
    ];

    protected $appends = ['associated_object', 'associated_object_type_slug'];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getAssociatedObjectAttribute()
    {
        return ActivityService::loadAssociation($this);
    }

    public function getAssociatedObjectTypeSlugAttribute()
    {
        return ActivityService::getAssociationKey($this);
    }
}

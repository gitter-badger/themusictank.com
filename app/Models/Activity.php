<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function associatedModel() {

        dd("Oh noes!");

        // return $this->hasOne(\App\Models\Track::class);
    }
}

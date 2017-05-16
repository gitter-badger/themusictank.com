<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'sub_id',
        'user_id'
    ];

    public function sub() {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

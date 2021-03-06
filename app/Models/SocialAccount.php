<?php

namespace App\Models;

class SocialAccount extends AppModel
{
    protected $fillable = [
        'user_id',
        'provider_user_id',
        'provider'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

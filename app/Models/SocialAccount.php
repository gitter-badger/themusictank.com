<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use Behavior\Dated;

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

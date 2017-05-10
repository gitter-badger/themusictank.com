<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use Behavior\Dated;

    protected $fillable = [
        'key',
        'value'
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{
    use Behavior\Dated;
    use Behavior\Validatable;
}
<?php

namespace App\Http\Controllers\Api;

use Exception;

Trait TakesAWhile
{
    protected function thisCouldTakeAWhile()
    {
        ini_set('max_execution_time', 0);
    }
}



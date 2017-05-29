<?php

namespace App\Http\Controllers\Api;

use Exception;

Trait RespondsJson
{
    protected function answer($data)
    {
        if (is_null($data)) {
            return [];
        }

        return $data;
    }

    protected function fail()
    {
        throw new Exception("Operation failed");
    }
}

<?php

namespace App\Models\Behavior;

use Illuminate\Validation\Validator;

trait Validatable
{
    protected $rules = [];

    public function validate($data)
    {
        $v = Validator::make($data, $this->rules);
        if ($v->fails()) {
            return $v->errors;
        }

        return null;
    }
}

<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Exceptions\AuthFailedException;

class Profiles extends Model
{
    public function login($email, $password)
    {
        return $this->post("profiles/login", [
            "json" => [
                "email"     => $email,
                "password"  => $password
            ]
        ]);
    }

}

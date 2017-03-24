<?php

namespace App\Models;

use App\Models\Restful\Model;
use App\Models\Profiles;

class ApiSessionToken extends Model
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

    public function logout($accessToken)
    {
        return $this->post("profiles/logout", [
            "query" => [
                "access_token" => $accessToken
            ]
        ]);
    }
}

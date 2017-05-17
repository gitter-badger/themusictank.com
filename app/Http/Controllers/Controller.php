<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function authUserId()
    {
        if ($this->hasActiveSession()) {
            return auth()->user()->id;
        }
    }

    protected function isAuthUser(User $user)
    {
        return $this->hasActiveSession() && auth()->user()->id !== $user->id;
    }

    protected function hasActiveSession()
    {
        return !is_null();
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Socialite;

class SocialController extends Controller
{
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback(SocialAccountService $service)
    {        
        $user = $service->createOrGetUser(Socialite::driver('facebook')->user());
        auth()->login($user);
        return redirect()->intended('/you/');
    }
}

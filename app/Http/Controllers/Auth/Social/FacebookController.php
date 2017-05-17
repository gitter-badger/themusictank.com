<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Socialite;

class FacebookController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback()
    {
        $service = new SocialAccountService();
        $user = $service->createOrGetUser($this->fromFacebook());
        
        auth()->login($user);
        return redirect()->intended('/you/');
    }

    protected function fromFacebook()
    {
        return Socialite::driver('facebook')->user();
    }
}

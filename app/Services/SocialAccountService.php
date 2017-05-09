<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountService
{
    public function getUser(ProviderUser $providerUser)
    {
        return SocialAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();
    }

    public function createUser(ProviderUser $providerUser)
    {
        $account = new SocialAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => 'facebook'
        ]);

        $user = User::whereEmail($providerUser->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'email' => $providerUser->getEmail(),
                'name' => $providerUser->getName(),
                'thumbnail' => $providerUser->getAvatar(),
            ]);
        }

        $account->user()->associate($user);
        $account->save();

        return $user;
    }

    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = $this->getUser($providerUser);
        if ($account) {
            return $account->user;
        }

        return $this->createUser($providerUser);
    }

}

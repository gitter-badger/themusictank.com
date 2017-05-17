<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountService
{

    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = $this->getLinkedUser($providerUser);
        if ($account) {
            return $account->user;
        }

        $user = $this->createUser($providerUser);
        $this->createSocialAccount($providerUser, $user);

        return $user;
    }

    protected function getLinkedUser(ProviderUser $providerUser)
    {
        return SocialAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();
    }

    protected function createUser(ProviderUser $providerUser)
    {
        $user = User::whereEmail($providerUser->getEmail())->first();

        if ($user) {
            return $user;
        }

        return User::create([
            'email' => $providerUser->getEmail(),
            'name' => $providerUser->getName(),
            'thumbnail' => $providerUser->getAvatar(),
        ]);
    }

    protected function createSocialAccount(ProviderUser $providerUser, User $user)
    {
        $account = new SocialAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => 'facebook'
        ]);
        $account->user()->associate($user);
        return $account->save();
    }

}

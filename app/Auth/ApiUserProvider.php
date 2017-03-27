<?php

namespace App\Auth;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Support\Facades\Auth;
use App\Models\ApiSessionToken;
use App\Models\Profiles;
use Exception;

class ApiUserProvider implements UserProviderInterface {

    protected $tokenService;
    protected $profileService;
    protected $userClass;

    public function __construct(ApiSessionToken $tokenService, Profiles $profileService, $userClass)
    {
        $this->tokenService = $tokenService;
        $this->profileService = $profileService;
        $this->userClass = $userClass;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        if ($this->hasUserInSession()) {
            return $this->getUserFromSession();
        }
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        if ($this->hasUserInSession()) {
            return $this->getUserFromSession();
        }
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        session(["loggedUser" => [
            "laravel_token" => $token,
            "authenticatable" => $user
        ]]);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        extract($credentials);
        $user = new $this->userClass();

        $token = $this->tokenService->login($email, $password);
        $user->setToken($token);

        $profile = $this->profileService->findById($token->userId);
        $user->setProfile($profile);

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return true;
    }

    private function getUserFromSession()
    {
        $sessionData = session("loggedUser");
        if (is_array($sessionData) && array_key_exists('authenticatable', $sessionData)) {
            return $sessionData['authenticatable'];
        }
    }

    private function hasUserInSession()
    {
        return !is_null($this->getUserFromSession());
    }
}

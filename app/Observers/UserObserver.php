<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Achievements\WelcomeToTmt;
use App\Services\AchievementService;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        AchievementService::grant(new WelcomeToTmt(), $user);
    }

}

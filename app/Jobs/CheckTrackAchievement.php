<?php

namespace App\Jobs;

use App\Models\Track;
use App\Models\User;
use App\Models\Achievements\Contributor;
use App\Services\AchievementService;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckTrackAchievement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $track;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Track $track, User $user)
    {
        $this->track = $track;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // reward all reviewing
        AchievementService::grant(new Contibutor(), $user);

        // reward by track id trigger
        $possibleAchievements = AchievementService::collectForTrack($this->track);
        foreach ($possibleAchievements as $achievement) {
            AchievementService::grant($achievement, $user, [
                "track" => $this->track
            ]);
        }
    }
}

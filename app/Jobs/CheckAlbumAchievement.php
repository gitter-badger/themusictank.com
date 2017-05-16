<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\User;
use App\Services\AchievementService;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckAlbumAchievement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $album;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Album $album, User $user)
    {
        $this->album = $album;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $possibleAchievements = AchievementService::collectForAlbum($this->album);
        foreach ($possibleAchievements as $achievement) {
            AchievementService::grant($achievement, $this->user, [
                "album" => $this->album
            ]);
        }
    }
}

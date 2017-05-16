<?php

namespace App\Jobs;

use App\Models\Artist;
use App\Models\User;
use App\Services\AchievementService;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckArtistAchievement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $artist;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Artist $artist, User $user)
    {
        $this->artist = $artist;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $possibleAchievements = AchievementService::collectForArtist($this->artist);
        foreach ($possibleAchievements as $achievement) {
            AchievementService::grant($achievement, $this->user, [
                "artist" => $this->artist
            ]);
        }
    }
}

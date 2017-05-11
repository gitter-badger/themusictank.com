<?php

namespace App\Jobs;

use App\Models\Track;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use App\Models\ReviewFrame;

class SaveReviewFrameDump implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $track;
    private $user;
    private $package;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Track $track, User $user, array $package)
    {
        $this->track = $track;
        $this->user = $user;
        $this->package = $package;
        $this->generatedAt = time();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now('utc')->toDateTimeString();

        foreach ($this->package as $idx => $pack) {
            $this->package[$idx]["user_id"] = (int)$this->user->id;
            $this->package[$idx]["track_id"] = (int)$this->track->id;
            $this->package[$idx]["groove"] = (float)$this->package[$idx]["groove"];
            $this->package[$idx]["position"] = (float)$this->package[$idx]["position"];
            $this->package[$idx]['created_at'] = $now;
            $this->package[$idx]['updated_at'] = $now;
        }

        if (ReviewFrame::insert($this->package)) {
            // Request an update to the global cache and the track cache
            dispatch(new UpdateReviewFrameCache($this->package, $this->track));
            dispatch(new UpdateReviewFrameCache($this->package, $this->track, $this->user));

            // Trigger the achievement lookups
            dispatch(new CheckArtistAchievement($this->track->artist, $this->user));
            dispatch(new CheckAlbumAchievement($this->track->album, $this->user));
            dispatch(new CheckTrackAchievement($this->track, $this->user));
        }
    }
}

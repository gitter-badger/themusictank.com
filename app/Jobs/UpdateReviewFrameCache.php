<?php

namespace App\Jobs;

use App\Models\Track;
use App\Models\User;
use App\Models\TrackReview;
use App\Services\GrooveAnalysisService;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateReviewFrameCache implements ShouldQueue
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
    public function __construct(array $package, Track $track, User $user = null)
    {
        $this->track = $track;
        $this->user = $user;
        $this->package = $package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->package)) {
            $groove = new GrooveAnalysisService($this->package);
            $keys = $this->getPrimaryKeys();

            foreach ($groove->calculate() as $grooveData) {
                // @todo: consider batch saving these
                $review = TrackReview::firstOrNew($keys);
                $review->fill($grooveData);
                $review->save();
            }
        }
    }

    protected function getPrimaryKeys()
    {
        return [
            "track_id" => $this->track->id,
            "user_id" => is_null( $this->user) ? null : $this->user->id
        ];
    }
}

<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Track;
use App\Jobs\SaveReviewFrameDump;
use App\Services\YoutubeService;

use App\Http\Controllers\Controller;

class TrackController extends Controller
{
    public function ytkey($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();

        if (is_null($track->youtube_key)) {
            $service = new YoutubeService(getenv('YOUTUBE_KEY'));
            $track->youtube_key = $service->getKey($track);
            $track->save();
        }

        return response()->json([
            'youtubekey' => $track->youtube_key
        ]);
    }

    public function saveCurvePart($slug)
    {
        dispatch(new SaveReviewFrameDump(
            Track::whereSlug($slug)->firstOrFail(),
            auth()->user(),
            (array)request('package')
        ));

        return response()->json([
            "rock" => "on"
        ]);
    }

    public function getNextTrack($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        return response()->json(
            $track->next()->first()
        );
    }
}

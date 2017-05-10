<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Track;
use App\Models\ReviewFrame;
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
        $track = Track::whereSlug($slug)->firstOrFail();
        $user = auth()->user();
        $package = request('package');

        foreach ((array)$package as $idx => $pack) {
            $package[$idx]["user_id"] = (int)$user->id;
            $package[$idx]["track_id"] = (int)$track->id;
        }

        ReviewFrame::create($package);

        return response()->json([
            "rock" => "on"
        ]);
    }

    public function getNextTrack($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        return response()->json(
            Track::next($track)->first()
        );
    }
}

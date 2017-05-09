<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Track;
use App\Services\YoutubeService;

use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function ytkey($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();

        if (is_null($track->youtube_key)) {
            $service = new YoutubeService(getenv('YOUTUBE_KEY'));
            $track->youtube_key = $service->getKey($track);
            $track->save();
        }

        return response()->json(['youtubekey' => $track->youtube_key]);
    }

    public function saveCurvePart($slug)
    {
        $track = Tracks::api()->findBySlug($slug);
        if (!$track) {
            return abort(404);
        }

        ReviewFrames::api()->savePartial(request('package'), $track, auth()->user()->getProfile());

        return response()->json(["rock" => "on"]);
    }

    public function getNextTrack($slug)
    {
        $track = Tracks::api()->findBySlug($slug);
        if (!$track) {
            return abort(404);
        }

        return response()->json(Tracks::api()->getNext($track));
    }
}

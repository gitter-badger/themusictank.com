<?php

namespace App\Http\Controllers;

use App\Models\Tracks;
use App\Models\TrackReviews;

class TrackController extends Controller
{
    public function show($slug)
    {
        $track = Tracks::api()->findBySlug($slug);
        if (!$track) {
            return abort(404);
        }

        $globalCurves = TrackReviews::api()->global($track);
        return view('tracks.show', compact('track', 'globalCurves'));
    }

    public function review($slug)
    {
        $track = Tracks::api()-findBySlug($slug);

        if (!$track) {
            return abort(404);
        }

        return view('tracks.review', compact('track'));
    }
}

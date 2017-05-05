<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\TrackReview;

class TrackController extends Controller
{
    public function show($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        $globalCurves = TrackReview::componentFields()->global($track)->get();
        return view('tracks.show', compact('track', 'globalCurves'));
    }

    public function review($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        return view('tracks.review', compact('track'));
    }
}

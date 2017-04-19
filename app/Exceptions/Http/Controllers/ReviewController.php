<?php

namespace App\Http\Controllers;

use App\Models\Profiles;
use App\Models\Tracks;

class ReviewController extends Controller
{
    public function showCurve($slug, $trackSlug)
    {
        $profile = Profiles::api()->findBySlug($slug);
        $track = Track::api()->findBySlug($slug);

        if (!$track) {
            return abort(404);
        }

        return view('reviews.show', compact('profile', 'track'));
    }
}

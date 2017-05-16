<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\User;
use App\Models\Album;
use App\Models\Artist;
use App\Jobs\UpdateReviewFrameCache;
use App\Services\Curves\ReviewCurve;

class AdminController extends Controller
{
    public function console()
    {
        $artistCount = Artist::count();
        $albumCount = Album::count();
        $trackCount = Track::count();

        return view('admin.console', compact('artistCount', 'albumCount', 'trackCount', 'apiRequests'));
    }

    public function resetReviewCache()
    {
        $trackId = request()->offsetGet('track_id');
        $userId = request()->offsetGet('user_id');

        $track = Track::find($trackId);
        $user = $userId ? Track::find($trackId) : null;

        $curve = new ReviewCurve($track, $user);
        $job = new UpdateReviewFrameCache($curve->filterFrames()->toArray(), $track, $user);
        $job->handle();

        return redirect('/admin/console')
            ->with('success', sprintf(
                'Cache reset [`%s`, `%s`]',
                $track->name,
                is_null($user) ? 'Global scope' : $user->name
            ));
    }

}

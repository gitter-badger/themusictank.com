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

        return view('admin.console', compact('artistCount', 'albumCount', 'trackCount');
    }

    public function resetReviewCache()
    {
        $trackId = (int)request('track_id');
        $userId = (int)request('user_id');

        $track = Track::find($trackId);
        $user = $userId ? User::find($userId) : null;

        $curve = new ReviewCurve($track, $user);
        $frames = $curve->filterFrames()->toArray();

        $job = new UpdateReviewFrameCache($frames, $track, $user);
        $job->handle();

        return redirect('/admin/console')
            ->with('success', sprintf(
                'Cache reset [`%s`, `%s`]',
                $track->name,
                is_null($user) ? 'Global scope' : $user->name
            ));
    }

}

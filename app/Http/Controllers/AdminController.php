<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\User;
use App\Models\Album;
use App\Models\Artist;
use App\Models\ReviewFrame;
use App\Jobs\UpdateReviewFrameCache;

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
        $data = request()->all();
        $track = Track::find($data['track_id']);
        $user = empty($data['user_id']) ? null : User::find($data['user_id']);

        $query = ReviewFrame::whereTrackId($track->id);
        if (!is_null($user)) {
            $query->whereUserId($user->id);
        }

        $job = new UpdateReviewFrameCache($query->get()->toArray(), $track, $user);
        $job->handle();

        return redirect('/admin/console')
            ->with('success', sprintf(
                'Cache reset [`%s`, `%s`]',
                $track->name,
                is_null($user) ? 'Global scope' : $user->name
            ));
    }

}

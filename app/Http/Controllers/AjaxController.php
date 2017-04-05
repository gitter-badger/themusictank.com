<?php

namespace App\Http\Controllers;

use App\Models\Artists;
use App\Models\Albums;
use App\Models\Tracks;
use App\Models\TrackUpvotes;
use App\Models\AlbumUpvotes;
use App\Models\Activities;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'ytkey']);
    }

    public function ytkey($slug)
    {
        $response = Tracks::api()->get("tracks/getYoutubeKey", [
            "query" => [
                "slug" => $slug
            ]
        ]);

        return response()->json($response);
    }

    public function upvoteTrack()
    {
        $vote = (int)request('vote');
        $objectId = (int)request('id');

        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        if (TrackUpvotes::shouldAddVote($vote)) {
            $response = TrackUpvotes::api()->vote($objectId, $profile->id, $vote);
            $profile->addTrackVote($objectId, $vote);

        } elseif (TrackUpvotes::shouldRemoveVote($vote)) {
            $response = TrackUpvotes::api()->removeVote($objectId, $profile->id);
            $profile->removeTrackVote($objectId);

        } else {
            return abort(404);
        }

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function upvoteAlbum()
    {
        $vote = (int)request('vote');
        $objectId = (int)request('id');

        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        if (AlbumUpvotes::shouldAddVote($vote)) {
            $response = AlbumUpvotes::api()->vote($objectId, $profile->id, $vote);
            $profile->addAlbumVote($objectId, $vote);

        } elseif (AlbumUpvotes::shouldRemoveVote($vote)) {
            $response = AlbumUpvotes::api()->removeVote($objectId, $profile->id);
            $profile->removeTrackVote($objectId);

        } else {
            return abort(404);
        }

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function artistSearch()
    {
        return response()->json(Artists::api()->search(request('q')));
    }

    public function trackSearch()
    {
        return response()->json(Tracks::api()->search(request('q')));
    }

    public function albumSearch()
    {
        return response()->json(Albums::api()->search(request('q')));
    }

    public function whatsUp()
    {
        $currentProfile = auth()->user()->getProfile();
        $timestamp = (int)request('timestamp');

        if ($timestamp > 0) {
            $dateTime = Carbon::createFromTimestamp()->toDateTimeString($timestamp);
            return response()->json((array)Activities::api()->findSince($dateTime, $currentProfile->id));
        }

        return response()->json((array)Activities::api()->findRecent($currentProfile->id, 5));
    }

    public function okstfu()
    {
        $currentProfile = auth()->user()->getProfile();
        return response()->json(Activities::api()->markAsReadByIds(request('ids'), $currentProfile->id));
    }
}

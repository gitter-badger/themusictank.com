<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Album;
use App\Models\Track;
use App\Models\User;
use App\Models\TrackUpvote;
use App\Models\AlbumUpvote;
use App\Models\Activity;
use App\Models\ReviewFrame;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AjaxController extends Controller
{
    public function ytkey($slug)
    {
        $response = Tracks::api()->get("tracks/getYoutubeKey", [
            "query" => [
                "slug" => $slug
            ]
        ]);

        return response()->json($response);
    }

    public function addTrackUpvote()
    {
        $vote = (int)request('vote');
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = TrackUpvotes::api()->vote($objectId, $profile->id, $vote);
        $profile->addTrackVote($objectId, $vote);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function addAlbumUpvote()
    {
        $vote = (int)request('vote');
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = AlbumUpvotes::api()->vote($objectId, $profile->id, $vote);
        $profile->addAlbumVote($objectId, $vote);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function removeTrackUpvote()
    {
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = TrackUpvotes::api()->removeVote($objectId, $profile->id);
        $profile->removeTrackVote($objectId);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function removeAlbumUpvote()
    {
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = AlbumUpvotes::api()->removeVote($objectId, $profile->id);
        $profile->removeTrackVote($objectId);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function artistSearch()
    {
        return response()->json(Artist::search(request('q'))->take(10)->get());
    }

    public function trackSearch()
    {
        return response()->json(Track::search(request('q'))->take(10)->get());
    }

    public function albumSearch()
    {
        return response()->json(Album::search(request('q'))->take(10)->get());
    }

    public function userSearch()
    {
        return response()->json(User::search(request('q'))->take(10)->get());
    }

    public function whatsUp()
    {
        $currentProfile = auth()->user()->getProfile();
        $timestamp = (int)request('timestamp');

        if ($timestamp > 0) {
            $dateTime = Carbon::createFromTimestamp($timestamp)->toDateTimeString();
            return response()->json((array)Activities::api()->findSince($dateTime, $currentProfile->id));
        }

        return response()->json((array)Activities::api()->findRecent($currentProfile->id, 5));
    }

    public function okstfu()
    {
        $currentProfile = auth()->user()->getProfile();
        return response()->json(
            Activities::api()
                ->markAsReadByIds(request('ids'), $currentProfile->id)
        );
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

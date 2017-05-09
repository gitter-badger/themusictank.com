<?php

namespace App\Http\Controllers\Ajax;

use App\Models\AlbumUpvotes;
use App\Models\TrackUpvotes;
use App\Http\Controllers\Controller;

class UpvoteController extends Controller
{

    public function addTrack()
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

    public function addAlbum()
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

    public function removeTrack()
    {
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = TrackUpvotes::api()->removeVote($objectId, $profile->id);
        $profile->removeTrackVote($objectId);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }

    public function removeAlbum()
    {
        $objectId = (int)request('id');
        $authUser = auth()->user();
        $profile = $authUser->getProfile();

        $response = AlbumUpvotes::api()->removeVote($objectId, $profile->id);
        $profile->removeTrackVote($objectId);

        $authUser->setProfile($profile, true);
        return response()->json($response);
    }
}

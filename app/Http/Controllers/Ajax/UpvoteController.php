<?php

namespace App\Http\Controllers\Ajax;

use App\Models\AlbumUpvote;
use App\Models\TrackUpvote;
use App\Services\UpvoteService;
use App\Http\Controllers\Controller;

class UpvoteController extends Controller
{
    public function addTrack()
    {
        $upvote = TrackUpvote::firstOrNew([
            'track_id' => (int)request('track_id'),
            'user_id' => $this->authUserId()
        ]);

        $upvote->vote = (int)request('vote');
        $upvote->save();

        return response()->json($upvote);
    }

    public function addAlbum()
    {
        $upvote = AlbumUpvote::firstOrNew([
            'album_id' => (int)request('album_id'),
            'user_id' => $this->authUserId()
        ]);

        $upvote->vote = (int)request('vote');
        $upvote->save();

        return response()->json($upvote);
    }

    public function removeTrack()
    {
        $upvote = TrackUpvote::where([
            'track_id' => (int)request('track_id'),
            'user_id' => $this->authUserId()
        ])->firstOrFail();

        return response()->json([
            "status" => $upvote->delete()
        ]);
    }

    public function removeAlbum()
    {
        $upvote = AlbumUpvote::where([
            'album_id' => (int)request('album_id'),
            'user_id' => $this->authUserId()
        ])->firstOrFail();

        return response()->json([
            "status" => $upvote->delete()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Tracks;
use App\Models\TrackUpvotes;
use App\Models\AlbumUpvotes;
use Illuminate\Http\Request;

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

        if ($vote < 0) {
            $response = TrackUpvotes::api()->removeVote(request('id'), auth()->user()->getProfile()->id);
        } else {
            $response = TrackUpvotes::api()->vote(
                request('id'),
                auth()->user()->getProfile()->id,
                $vote
            );
        }
        return response()->json($response);
    }

    public function upvoteAlbum()
    {
        $vote = (int)request('vote');

        if ($vote < 0) {
            $response = AlbumUpvotes::api()->removeVote(request('id'), auth()->user()->getProfile()->id);
        } else {
            $response = AlbumUpvotes::api()->vote(
                request('id'),
                auth()->user()->getProfile()->id,
                $vote
            );
        }

        return response()->json($response);
    }
}

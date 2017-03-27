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

    public function upvote($type)
    {
        if ($type === "track") {
            return $this->upvoteTrack();
        }

        if ($type === "album") {
            return $this->upvoteAlbum();
        }

        return abort(404);
    }

    public function upvoteTrack()
    {
        $response = TrackUpvotes::api()->vote(
            request('id'),
            request('artistid'),
            auth()->user()->getProfile()->id,
            request('type')
        );

        return view('partials.buttons.upvote', [
            'type' => "track",
            'id' => request('id'),
            'artistid' => request('artistid')
        ]);
    }

    public function upvoteAlbum()
    {
        AlbumUpvotes::api()->vote(
            request()('id'),
            request('artistid'),
            auth()->user()->id,
            request('type')
        );

        return view('partials.buttons.upvote', [
            'type' => "album",
            'id' => request('id'),
            'artistid' => request('artistid')
        ]);
    }
}

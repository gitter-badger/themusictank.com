<?php

namespace App\Http\Controllers;

use App\Models\Tracks;

class TrackController extends Controller
{
    public function show($slug)
    {
        $track = $this->loadTrackData($slug);

        if (!$track) {
            return abort(404);
        }

        return view('tracks.show', compact('track'));
    }

    public function review($slug)
    {
        $track = $this->loadTrackData($slug);

        if (!$track) {
            return abort(404);
        }

        return view('tracks.review', compact('track'));
    }

    private function loadTrackData($slug)
    {
        return Tracks::api()->first("tracks", [
            "query" => [
                "filter" => [
                    "where" => ["slug" =>  $slug],
                    "include" => ["album", "artist"]
                ]
            ]
        ]);
    }

}

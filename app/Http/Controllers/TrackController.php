<?php

namespace App\Http\Controllers;

use App\Models\Tracks;

class TrackController extends Controller
{
    public function show($slug)
    {
        $track = Tracks::api()->first("tracks", [
            "query" => [
                "filter" => [
                    "where" => ["slug" =>  $slug],
                    "include" => ["album", "artist"]
                ]
            ]
        ]);

        if (!$track) {
            return abort(404);
        }

        return view('tracks.show', compact('track'));
    }
}

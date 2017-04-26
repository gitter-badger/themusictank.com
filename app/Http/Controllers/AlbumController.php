<?php

namespace App\Http\Controllers;

use App\Models\Albums;

class AlbumController extends Controller
{
    public function show($slug)
    {
        $album = Albums::api()->first("albums", [
            "query" => [
                "filter" => [
                    "where" => ["slug" =>  $slug],
                    "include" => ["artist", "tracks"]
                ]
            ]
        ]);

        if (!$album) {
            return abort(404);
        }

        return view('albums.show', compact('album'));
    }
}

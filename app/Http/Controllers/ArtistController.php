<?php

namespace App\Http\Controllers;

use App\Models\Artists;

class ArtistController extends Controller
{
    public function show($slug)
    {
        $artist = Artists::api()->first("artists", [
            "query" => [
                "filter" => [
                    "where" => ["slug" =>  $slug],
                    "include" => "albums"
                ]
            ]
        ]);

        if (!$artist) {
            return abort(404);
        }

        return view('artists.show', compact('artist'));
    }
}

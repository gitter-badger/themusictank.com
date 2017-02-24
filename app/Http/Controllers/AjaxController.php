<?php

namespace App\Http\Controllers;

use App\Models\Tracks;

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
}

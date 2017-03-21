<?php

namespace App\Http\Controllers;

use App\Models\Artists;

class ArtistController extends Controller
{
    public function index()
    {
        $featuredArtists = Artists::api()->findTopFeatured();
        $spotlightArtist = array_pop($featuredArtists);

        return view('artists.index', compact('spotlightArtist', 'featuredArtists'));
    }

    public function show($slug)
    {
        $artist = Artists::api()->findBySlug($slug);

        if (!$artist) {
            return abort(404);
        }

        return view('artists.show', compact('artist'));
    }
}

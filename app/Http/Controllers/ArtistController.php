<?php

namespace App\Http\Controllers;

use App\Models\Artist;

class ArtistController extends Controller
{
    public function index()
    {
        $collection = Artist::whereIsFeatured(1)->take(11)->get();
        $spotlightArtist = $collection->shift()->first();
        $featuredArtists = $collection;

        return view('artists.index', compact('spotlightArtist', 'featuredArtists'));
    }

    public function show($slug)
    {
        $artist = Artist::whereSlug($slug)->firstOrFail();
        return view('artists.show', compact('artist'));
    }

}

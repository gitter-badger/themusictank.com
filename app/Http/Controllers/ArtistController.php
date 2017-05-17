<?php

namespace App\Http\Controllers;

use App\Models\Artist;

class ArtistController extends Controller
{
    public function index()
    {
        $collection = Artist::whereIsFeatured(true)->take(11)->get();
        $spotlightArtist = $collection->first();
        $featuredArtists = $collection->slice(1);

        return view('artists.index', compact('spotlightArtist', 'featuredArtists'));
    }

    public function show($slug)
    {
        $artist = Artist::whereSlug($slug)->firstOrFail();
        
        return view('artists.show', compact('artist'));
    }

}

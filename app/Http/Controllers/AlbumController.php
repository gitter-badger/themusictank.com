<?php

namespace App\Http\Controllers;

use App\Models\Album;

class AlbumController extends Controller
{
    public function show($slug)
    {
        $album = Album::whereSlug($slug)->firstOrFail();
        
        return view('albums.show', compact('album'));
    }
}

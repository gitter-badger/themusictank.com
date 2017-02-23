<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('page.home', [
            "nbArtists" => 100,
            "nbAlbums" => 100,
            "nbTracks" => 100,
        ]);
    }

    public function about()
    {
        return view('page.about');
    }


    public function legal()
    {
        return view('page.legal');
    }
}

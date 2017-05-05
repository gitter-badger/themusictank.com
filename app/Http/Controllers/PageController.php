<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Album;
use App\Models\Artist;

class PageController extends Controller
{
    public function home()
    {
        $artistCount = Artist::count();
        $albumCount = Album::count();
        $trackCount = Track::count();

        return view('page.home', compact('artistCount', 'albumCount', 'trackCount'));
    }

    public function about()
    {
        return view('page.about');
    }

    public function legal()
    {
        return view('page.legal');
    }

    public function apiIsDown()
    {
        return view('errors/api/down');
    }

    public function apiError()
    {
        return view('errors/api/error');
    }
}

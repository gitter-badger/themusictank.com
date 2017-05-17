<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Album;
use App\Models\Artist;
use App\Models\User;

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
        $francois = User::find(1);
        $julien = User::find(2);

        return view('page.about', compact('francois', 'julien'));
    }

    public function legal()
    {
        return view('page.legal');
    }
}

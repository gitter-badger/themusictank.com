<?php

namespace App\Http\Controllers;

use App\Models\Tracks;
use App\Models\Albums;
use App\Models\Artists;

class PageController extends Controller
{
    public function home()
    {
        $artistCount = Artists::api()->fetchCount()->count;
        $albumCount = Albums::api()->fetchCount()->count;
        $trackCount = Tracks::api()->fetchCount()->count;

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
}

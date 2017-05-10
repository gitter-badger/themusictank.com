<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Artist;
use App\Models\Track;
use App\Models\Album;
use App\Models\User;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function album()
    {
        $searchResults = Album::search(request('q'))->take(10)->get();
        return response()->json($searchResults);
    }

    public function artist()
    {
        $searchResults = Artist::search(request('q'))->take(10)->get();
        return response()->json($searchResults);
    }

    public function track()
    {
        $searchResults = Track::search(request('q'))->take(10)->get();
        return response()->json($searchResults);
    }

    public function user()
    {
        $searchResults = User::search(request('q'))->take(10)->get();
        return response()->json($searchResults);
    }
}

<?php

namespace App\Http\Controllers\Ajax;

use App\Models\Artist;
use App\Models\Track;
use App\Models\Album;
use App\Models\User;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function artist()
    {
        return $this->answer(Artist::search(request('q'))->take(10)->get());
    }

    public function track()
    {
        return $this->answer(Track::search(request('q'))->take(10)->get());
    }

    public function album()
    {
        return $this->answer(Album::search(request('q'))->take(10)->get());
    }

    public function user()
    {
        return $this->answer(User::search(request('q'))->take(10)->get());
    }

    protected function answer($dataset)
    {
        return response()->json($dataset);
    }
}

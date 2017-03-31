<?php

namespace App\Http\Controllers;

use App\Models\Tracks;
use App\Models\Albums;
use App\Models\Artists;
use App\Models\ApiRequests;

class AdminController extends Controller
{
    public function console()
    {
        $artistCount = Artists::api()->fetchCount()->count;
        $albumCount = Albums::api()->fetchCount()->count;
        $trackCount = Tracks::api()->fetchCount()->count;
        $apiRequests = ApiRequests::api()->fetch();


        $album = null;
        return view('admin.console', compact('artistCount', 'albumCount', 'trackCount', 'apiRequests'));
    }

}

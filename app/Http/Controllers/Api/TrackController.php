<?php

namespace App\Http\Controllers\Api;

use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends ApiController
{
    public function show($slug)
    {
        return $this->answer(Track::where(['slug' => $slug])->first());
    }   
    
    public function update(Request $request, $gid)
    {
        $track = Track::where(['gid' => $gid])->first();

        if (is_null($track)) {
            $track = new Track();
            $track->gid = $gid;
        }

        $track->fill($request->json()->all());

        if ($track->save()) {
            return $this->answer($track);
        }

        return $this->fail();
    }
}

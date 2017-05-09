<?php

namespace App\Http\Controllers\Api;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends ApiController
{
    public function show($slug)
    {
        return $this->answer(Album::where(['slug' => $slug])->first());
    }   
    
    public function update(Request $request, $gid)
    {
        $album = Album::where(['gid' => $gid])->first();

        if (is_null($album)) {
            $album = new Album();
            $album->gid = $gid;
        }

        $album->fill($request->json()->all());

        if ($album->save()) {
            return $this->answer($album);
        }

        return $this->fail();
    }
}

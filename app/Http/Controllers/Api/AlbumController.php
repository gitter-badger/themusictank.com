<?php

namespace App\Http\Controllers\Api;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends ApiController
{
    public function show($slug)
    {
        return $this->answer(Album::where(['slug' => $slug])->firstOrFail());
    }

    public function update(Request $request, $slug)
    {
        $album = Album::where(['slug' => $slug])->firstOrFail();
        $album->fill($request->json()->all());

        if ($album->save()) {
            return $this->answer($album);
        }

        return $this->fail();
    }
}

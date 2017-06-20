<?php

namespace App\Http\Controllers\Api;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends ApiController
{
    public function show($slug)
    {
        return $this->answer(Artist::where(['slug' => $slug])->firstOrFail());
    }

    public function update(Request $request, $slug)
    {
        $artist = Artist::where(['slug' => $slug])->firstOrFail();
        $artist->fill($request->json()->all());

        if ($artist->save()) {
            return $this->answer($artist);
        }

        return $this->fail();
    }
}

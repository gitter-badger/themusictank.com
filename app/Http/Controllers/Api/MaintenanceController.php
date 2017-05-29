<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArtistDiscog;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    use RespondsJson;

    public function artistSyncRequired(Request $request)
    {
        $ids = (array)$request->json("ids");
        $states = (array)$request->json("states");

        $outOfSync = ArtistDiscog::whereIn("discog_id", $ids)
            ->whereNotIn("state", $states)
            ->select("discog_id")
            ->get();

        return $this->answer([
            "ids" => $outOfSync->toArray()
        ]);
    }

    public function artistExistance(Request $request)
    {
        $ids = (array)$request->json("ids");
        $existingIds = ArtistDiscog::whereIn("discog_id", $ids)
            ->select("discog_id")
            ->pluck("discog_id");

        $newIds = array_filter($ids, function($id) use ($existingIds) {
            return !$existingIds->contains($id);
        });

        return $this->answer([
            "ids" => $newIds
        ]);
    }

    public function artistSync(Request $request)
    {


        $album = Album::firstOrNew(['gid' => $gid]);
        $album->fill($request->json()->all());

        if ($album->save()) {
            return $this->answer($album);
        }

        return $this->fail();
    }
}

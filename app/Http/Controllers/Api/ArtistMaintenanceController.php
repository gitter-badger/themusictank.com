<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArtistDiscog;
use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistMaintenanceController extends Controller
{
    use RespondsJson;

    public function syncRequired(Request $request)
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

    public function sync(Request $request)
    {
        ini_set('max_execution_time', 0);

        $status = true;
        $rawArtists = (array)$request->json('artists');

        array_walk($rawArtists, function($raw) use ($status) {
            $discog = ArtistDiscog::firstOrNew(['discog_id' => $raw['discog_id']]);
            $discog->fill($raw);

            $artistData = ['name' => $raw['name']];
            if ($discog->artist()->count() === 0) {
                $artist = Artist::create($artistData);
                $discog->artist()->associate($artist);
            } else {
                $discog->artist()->fill($artistData);
            }

            $status = $status && $discog->save();
        });

        return $this->answer([
            "status" => $status
        ]);
    }
}

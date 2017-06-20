<?php

namespace App\Http\Controllers\Api\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\TrackDiscog;
use App\Models\Track;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RespondsJson;
use App\Http\Controllers\Api\TakesAWhile;

class TrackMaintenanceController extends Controller
{
    use RespondsJson,
        TakesAWhile;

    public function syncRequired(Request $request)
    {
        $ids = (array)$request->json("ids");
        $states = (array)$request->json("states");

        $outOfSync = TrackDiscog::whereIn("discog_id", $ids)
            ->whereNotIn("state", $states)
            ->select("discog_id")
            ->get();

        return $this->answer([
            "ids" => $outOfSync->toArray()
        ]);
    }

    public function sync(Request $request)
    {
        $this->thisCouldTakeAWhile();

        $status = true;
        array_walk((array)$request->json('tracks'), function($raw) use ($status) {
            $discog = TrackDiscog::firstOrNew(['discog_id' => $raw['discog_id']]);
            $discog->fill($raw);

            $trackData = ['name' => $raw['title']];
            if ($discog->track()->count() === 0) {
                $discog->track()->associate(Track::create($trackData));
            } else {
                $discog->track()->fill($trackData);
            }

            $status = $status && $discog->save();
        });

        return $this->answer([
            "status" => $status
        ]);
    }
}

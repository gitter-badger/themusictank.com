<?php

namespace App\Http\Controllers\Api\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\AlbumDiscog;
use App\Models\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RespondsJson;
use App\Http\Controllers\Api\TakesAWhile;

class AlbumMaintenanceController extends Controller
{
    use RespondsJson,
        TakesAWhile;

    public function syncRequired(Request $request)
    {
        $ids = (array)$request->json("ids");
        $states = (array)$request->json("states");

        $outOfSync = AlbumDiscog::whereIn("discog_id", $ids)
            ->whereNotIn("state", $states)
            ->select("discog_id")
            ->get();

        return $this->answer([
            "ids" => $outOfSync->toArray()
        ]);
    }

    public function missingThumbnails(Request $request)
    {
        $expiredDiscogIds = Album::whereNull('thumbnail')
            ->take(100)
            ->get()
            ->map(function(Album $album){
                return [
                    'discog_id' => $album->discog()->discog_id,
                    'album_id' => $album->id,
                    'slug' =>  $album->slug
                ];
            });

        return $this->answer([
            "ids" => $expiredDiscogIds
        ]);
    }

    public function sync(Request $request)
    {
        $this->thisCouldTakeAWhile();

        $status = true;
        array_walk((array)$request->json('masters'), function($raw) use ($status) {
            $discog = AlbumDiscog::firstOrNew(['discog_id' => $raw['discog_id']]);
            $discog->fill($raw);

            $albumData = ['name' => $raw['title']];
            if ($discog->album()->count() === 0) {
                $discog->album()->associate(Album::create($albumData));
            } else {
                $discog->album()->fill($albumData);
            }

            $status = $status && $discog->save();
        });

        return $this->answer([
            "status" => $status
        ]);
    }
}

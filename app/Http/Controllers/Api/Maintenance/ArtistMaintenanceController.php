<?php

namespace App\Http\Controllers\Api\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\ArtistDiscog;
use App\Models\AlbumDiscog;
use App\Models\TrackDiscog;
use App\Models\Artist;
use App\Models\Album;
use App\Models\Track;
use Illuminate\Http\Request;
use DB;
use Exception;
use App\Http\Controllers\Api\RespondsJson;
use App\Http\Controllers\Api\TakesAWhile;

class ArtistMaintenanceController extends Controller
{
    use RespondsJson,
        TakesAWhile;

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

    public function missingThumbnails(Request $request)
    {
        $expiredDiscogIds = Artist::whereNull('thumbnail')
            ->take(100)
            ->get()
            ->map(function(Artist $artist){
                return [
                    'discog_id' => $artist->discog()->discog_id,
                    'artist_id' => $artist->id,
                    'slug' =>  $artist->slug
                ];
            });

        return $this->answer([
            "ids" => $expiredDiscogIds
        ]);
    }


    public function add(Request $request)
    {
        $raw = (array)$request->json('artist');
        $this->thisCouldTakeAWhile();

        DB::beginTransaction();

        try {
            if (ArtistDiscog::whereDiscogId($raw['discog_id'])->count()) {
                throw new Exception("This record already exists");
            }

            $artist = Artist::create(['name' => $raw['name']]);

            $discog = new ArtistDiscog();
            $discog->fill($raw);
            $discog->artist()->associate($artist);
            $discog->save();

            foreach ((array)$raw['albums'] as $rawAlbum) {

                $album = Album::create([
                    'name' => $rawAlbum['title'],
                    "artist_id" => $artist->id
                ]);

                $albumDiscog = new AlbumDiscog();
                $albumDiscog->fill($rawAlbum);
                $albumDiscog->album()->associate($album);
                $albumDiscog->save();

                foreach ((array)$rawAlbum['trackList'] as $rawTrack) {
                    $track = Track::create([
                        "name" => $rawTrack["title"],
                        "artist_id" => $artist->id,
                        "album_id" => $album->id
                    ]);
                    $track->fill($rawTrack);
                    $track->save();

                    $trackDiscog = new TrackDiscog();
                    $trackDiscog->fill($rawTrack);
                    $trackDiscog->track()->associate($track);
                    $trackDiscog->save();
                }
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $this->answer([
            "status" => true
        ]);
    }

    public function sync(Request $request)
    {
        $this->thisCouldTakeAWhile();

        $status = true;
        array_walk((array)$request->json('artists'), function($raw) use ($status) {
            $discog = ArtistDiscog::firstOrNew(['discog_id' => $raw['discog_id']]);
            $discog->fill($raw);

            $artistData = ['name' => $raw['name']];
            if ($discog->artist()->count() === 0) {
                $discog->artist()->associate(Artist::create($artistData));
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

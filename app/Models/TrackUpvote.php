<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackUpvote extends Model
{
    public function track() {
        return $this->belongsTo(\App\Models\Track::class);
    }

    public function album() {
        return $this->belongsTo(\App\Models\Album::class);
    }

    public static function vote() {
// "json" => [
//                 "trackId" => (int)$id,
//                 "profileId" => (int)$profileId,
//                 "vote" => (int)$vote
//             ],
//             "query" => [
//                 "where" => [
//                     "profileId" => (int)$profileId,
//                     "trackId" => (int)$id
//                 ],
//             ]


    }


}

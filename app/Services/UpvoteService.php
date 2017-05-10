<?php

namespace App\Services;

use \App\Models\TrackUpvote;
use \App\Models\AlbumUpvote;

class UpvoteService
{
    private $type;
    private $user;
    private $objectId;
    private $vote;

    public function __construct($type, \App\Models\User $user, $objectId, $vote)
    {
        $this->type = $type;
        $this->user = $user;
        $this->objectId = $objectId;
        $this->vote = $vote;
    }

    public function vote()
    {
        $vote = $this->type === "track" ?
            $this->getTrackVote() :
            $this->getAlbumVote();

        $vote->vote = $this->vote;
        $vote->save();

        return $vote;
    }

    protected function getTrackVote()
    {
        return TrackUpvote::firstOrNew([
            'track_id' => $this->objectId,
            'user_id' => $this->user->id,
        ]);
    }

    protected function getAlbumVote()
    {
        return AlbumUpvote::firstOrNew([
            'album_id' => $this->objectId,
            'user_id' => $this->user->id,
        ]);
    }
}

<?php

namespace App\Models\Entities;

use JsonSerializable;

class Profile implements JsonSerializable
{
	public $realm;
	public $username;
	public $password;
	public $email;
	public $emailverified;
	public $verificationtoken;
	public $id;
	public $role;
	public $is_trendmaker;
	public $slug;
	public $name;
    public $albumUpvotes = [];
    public $trackUpvotes = [];
    public $activities = [];

    public function jsonSerialize()
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'slug' => $this->slug,
            'id' => $this->id,
            'name' => $this->name,
            'activities' => $this->activities,
        ];
    }

    public function getUpvotes()
    {
        return [
            'albumUpvotes' => $this->albumUpvotes,
            'trackUpvotes' => $this->trackUpvotes,
        ];
    }

    public function trackVoteExists($objectId)
    {
        return $this->getTrackVoteIndexById($objectId) > -1;
    }

    public function albumVoteExists($objectId)
    {
        return $this->getAlbumVoteIndexById($objectId) > -1;
    }

    public function addTrackVote($objectId, $vote)
    {
        if (!$this->trackVoteExists($objectId)) {
            $upvote = new TrackUpvote();
            $upvote->trackId = $objectId;
            $upvote->vote = $vote;

            $this->trackUpvotes[] = $upvote;
        }
    }

    public function removeTrackVote($objectId)
    {
        $idx = $this->getTrackVoteIndexById($objectId);
        unset($this->trackUpvotes[$idx]);
        $this->trackUpvotes = array_values($this->trackUpvotes);
    }

    public function addAlbumVote($objectId, $vote)
    {
        if (!$this->albumVoteExists($objectId)) {
            $upvote = new AlbumUpvote();
            $upvote->albumId = $objectId;
            $upvote->vote = $vote;

            $this->albumUpvotes[] = $upvote;
        }
    }

    public function removeAlbumVote($objectId)
    {
        $idx = $this->getAlbumVoteIndexById($objectId);
        unset($this->albumUpvotes[$idx]);
        $this->albumUpvotes = array_values($this->albumUpvotes);
    }

    private function getTrackVoteIndexById($objectId)
    {
        foreach($this->trackUpvotes as $idx => $vote) {
            if ((int)$vote->trackId === (int)$objectId) {
                return $idx;
            }
        }

        return -1;
    }

    private function getAlbumVoteIndexById($objectId)
    {
        foreach($this->albumUpvotes as $idx => $vote) {
            if ((int)$vote->albumId === (int)$objectId) {
                return $idx;
            }
        }

        return -1;
    }
}

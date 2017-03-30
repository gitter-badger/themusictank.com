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

    public function jsonSerialize()
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'slug' => $this->slug,
            'name' => $this->name,
            'albumUpvotes' => $this->albumUpvotes,
            'trackUpvotes' => $this->trackUpvotes,
        ];
    }

    public function addTrackVote($objectId, $vote)
    {
        $key = "$objectId";
        if (!array_key_exists($key, $this->trackUpvotes)) {
            $this->trackUpvotes[$key] = new TrackUpvote();
        }

        $this->trackUpvotes[$key]->vote = $vote;
    }

    public function removeTrackVote($key)
    {
        unset($this->trackUpvotes["$key"]);
    }

    public function addAlbumVote($objectId, $vote)
    {
        $key = "$objectId";
        if (!array_key_exists($key, $this->albumUpvotes)) {
            $this->albumUpvotes[$key] = new AlbumUpvote();
        }

        $this->albumUpvotes[$key]->vote = $vote;
    }

    public function removeAlbumVote($key)
    {
        unset($this->albumUpvotes["$key"]);
    }
}

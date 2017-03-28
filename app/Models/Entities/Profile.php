<?php

namespace App\Models\Entities;

class Profile
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

    public function exportToJson()
    {
        return json_encode([
            "upvotes" => [
                "tracks" => array_column($this->trackUpvotes, "id"),
                "albums" => array_column($this->albumUpvotes, "id")
            ]
        ]);
    }

}

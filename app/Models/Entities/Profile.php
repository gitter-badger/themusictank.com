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
                "tracks" => $this->simplifyUpvoteArray($this->trackUpvotes),
                "albums" => $this->simplifyUpvoteArray($this->albumUpvotes, "albumId")
            ]
        ]);
    }

    private function simplifyUpvoteArray($source, $primaryKey = "trackId")
    {
        $return = [];

        foreach ($source as $row) {
            $return[$row->{$primaryKey} . ""] = $row->vote;
        }

        return $return;
    }

}

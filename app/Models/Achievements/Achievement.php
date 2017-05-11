<?php

namespace App\Models\Achievements;

class Achievement {

    public $id = -1;
    public $name = "Undefined Achievement";
    public $description = "This means something is broken.";

    protected $trackIdsTriggers = [];
    protected $albumIdsTriggers = [];
    protected $artistIdsTriggers = [];

    /**
     * Allow additional processing. If not additional parameters
     * are sent, assume the achivement grant applies to the user
     **/
    public function applies($user, $additional = [])
    {
        return count($additional) === 0;
    }

}

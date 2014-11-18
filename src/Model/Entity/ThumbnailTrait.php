<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\Error\Exception;
use Cake\ORM\TableRegistry;

trait ThumbnailTrait {

    public function getImageUrl($type = "thumb")
    {
        if (!is_null($this->get("image"))) {
            // We could check if the file exists before trying to load it, but I'm pretty
            // sure it will be way too resource intensive.
            return "//static.themusictank.com/" . $this->get("image");
        }

        return "/img/placeholder.png";
    }
}

<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\Error\Exception;
use Cake\ORM\TableRegistry;

trait ThumbnailTrait {

    public function getImageUrl($type = "thumb")
    {
        if (!is_null($this->get("image"))) {
           /* // When the converter is not available, assume we are in dev and pull
            // the image right from tmt.com.
            if (is_null(Configure::read('ConvertCMD'))) {
                return sprintf("//themusictank.com/img/cache/%s_%s.jpg", $this->image, $type);
            } */

            // Otherwise, load the requested image if it exists.
            if (file_exists(WWW_ROOT . "img" . DS . "cache" . DS . $this->get("image") . "_" . $type . ".jpg")) {
                return "/img/cache/" . $this->get("image"). "_" . $type . ".jpg";
            }
        }

        return "/img/placeholder.png";
    }
}

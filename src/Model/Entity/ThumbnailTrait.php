<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\Error\Exception;

trait ThumbnailTrait {

    public function getImageUrl($type = "thumb")
    {
        if (!$this->_isSupportedType($type)) {
            $msg = sprintf("Invalid image type '%s'", $type);
            throw new Exception($msg);
        }

        if (!is_null($this->image)) {
            // When the converter is not available, assume we are in dev and pull
            // the image right from tmt.com.
            if (is_null(Configure::read('ConvertCMD'))) {
                return sprintf("//themusictank.com/img/cache/%s_%s.jpg", $this->image, $type);
            }

            // Otherwise, load the requested image if it exists.
            if (file_exists(WWW_ROOT . "img" . DS . "cache" . DS . $this->image . "_" . $type . ".jpg")) {
                return "/img/cache/" . $this->image . "_" . $type . ".jpg";
            }
        }

        return "/img/placeholder.png";
    }

    public function getSupportedTypes()
    {
        return ["thumb", "big", "blur"];
    }

    private function _isSupportedType($type) {
        return in_array($type, $this->getSupportedTypes());
    }

}

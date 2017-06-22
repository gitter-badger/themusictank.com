<?php

namespace App\Models\Behavior;

trait Thumbnailed
{
    protected function getThumbnailPrefixFor($type)
    {
        return sprintf("%s/%s_%s.jpg", strtolower(class_basename($this)) . "s", $this->slug, $type);
    }

    public function getThumbnailUrl($type = "thumb")
    {
        if ((bool)$this->thumbnail) {
            return "http://static.themusictank.com/" . $this->getThumbnailPrefixFor($type);
        }

        return "http://static.themusictank.com/assets/images/placeholder.png";
    }
}

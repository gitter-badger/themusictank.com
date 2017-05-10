<?php

namespace App\Models\Behavior;

trait Thumbnailed
{
    protected function getThumbnailPrefix()
    {
        if (!is_null($this->slug) && count($this->slug) > 1) {
            $first = substr(0, 1, $this->slug);
            $second = substr(1, 1, $this->slug);
            $pattern = sprintf("%s/%s/%s/%s", get_called_class(), $first, $second, $this->slug);
            return  $pattern;
        }
    }

    protected function getThumbnailPrefixFor($type)
    {
        $prefix = $this->getThumbnailPrefix();
        if (!is_null($prefix)) {
            return sprintf("%s-%s.jpg", $prefix, $type);
        }
    }

    public function getThumbnailUrl($type = "thumb")
    {
        if (is_null($this->thumbnail)) {
            return "//static.themusictank.com/assets/images/placeholder.png";
        }

        return "//static.themusictank.com/" . $this->getThumbnailPrefixFor($type);
    }
}

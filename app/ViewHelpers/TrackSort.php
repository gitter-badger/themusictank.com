<?php

namespace App\ViewHelpers;

use Illuminate\Support\Collection;

class TrackSort {

    private $data = [];

    public function __construct($trackCollection)
    {
        $this->data = $this->sort($trackCollection);
    }

    public function first()
    {
        $first = $this->data->first();
        if (Collection::class === get_class($first)) {
            return $first->first();
        }

        return $first;
    }

    public function all()
    {
        return $this->data;
    }

    protected function sort($trackCollection)
    {
        $albumsNames = ["none"];
        $albumGroupings = [[]];

        foreach ($trackCollection as $track) {
            if ($track->isSupportLabel()) {
                $albumsNames[] = $track->name;
            } elseif ($track->isPartOfMany()) {
                $albumGroupings[$track->getSupportIndex()][$track->getRealPosition()] = $track;
            } else {
                $albumGroupings[0][$track->getRealPosition()] = $track;
            }
        }

        $out = [];
        foreach ($albumGroupings as $idx => $group) {
            if (count($group)) {
                $index = array_key_exists($idx, $albumsNames) ? $albumsNames[$idx] : $albumsNames[0];
                ksort($group);
                $out[$index] = collect($group);
            }
        }

        return collect($out);
    }

}

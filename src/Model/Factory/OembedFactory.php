<?php

namespace App\Model\Factory;

use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;

class OembedFactory {

    public static function getObjectFromUrl($url)
    {
        $pattern = explode("/", preg_replace('/http:\/\//', "", $url));

        if (count($pattern) < 3 && count($pattern) > 4) {
            return null;
        }

        // TMT only supports these types atm
        $model = $pattern[1];
        if(!preg_match('/artists|albums|tracks/i', $model)) {
            return null;
        }

        return TableRegistry::get(ucfirst($model));
    }

    public static function getSlugFromUrl($url)
    {
        $pattern = explode("/", preg_replace('/http:\/\//', "", $url));

        if (count($pattern) < 3 && count($pattern) > 4) {
            return null;
        }

        return $pattern[3];
    }

}




<?php

namespace App\Models\Restful;

use Illuminate\Support\Facades\Cache as CacheFacade;

class Cache
{
    private $key;

    public function setContext($type, $endpoint, $params = [])
    {
        $this->key = sha1($type . $endpoint . json_encode($params));
    }

    public function exists()
    {
        return !(bool)getenv("APP_DEBUG") && CacheFacade::has($this->key);
    }

    public function set($data, $timeoutMinutes = 360000)
    {
        CacheFacade::put($this->key, $data, $timeoutMinutes);
    }

    public function get()
    {
        return CacheFacade::get($this->key);
    }
}

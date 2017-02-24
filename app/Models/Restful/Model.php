<?php

namespace App\Models\Restful;

use GuzzleHttp\Client;

class Model
{
    public static function api()
    {
        $client = new Client([
            'base_uri' => getenv("TMT_API") ?
                getenv("TMT_API") :
                "http://localhost:3000/v1/"
        ]);

        return new static($client, new Cache(), new ModelParser());
    }

    private $client;
    private $cache;
    private $parser;

    public $hasMany = [];
    public $belongsTo = [];

    public function __construct(Client $client, Cache $cache, ModelParser $parser)
    {
        $this->client = $client;
        $this->cache = $cache;

        $this->parser = $parser;
        $this->parser->setContext($this);
    }

    public function first($endpoint, $data)
    {
        $data = $this->get($endpoint, $data);
        if (is_array($data) && count($data) > 0) {
            return $data[0];
        }
    }

    public function get($endpoint, $data = [])
    {
        $this->cache->setContext("get", $endpoint, $data);

        if (!$this->cache->exists()) {
            $response = $this->client->get($endpoint, $this->appendAccessToken($data));
            $this->cache->set($this->parser->parseResponse($response));
        }

        return $this->cache->get();
    }

    // public function post($endpoint, $data = [])
    // {
    //     $response = $this->client->post($endpoint, $this->appendAccessToken($data));
    //     return $this->parseResponseToJson($response);
    // }

    // public function patch($endpoint, $data = [])
    // {
    //     $response = $this->client->patch($endpoint, $this->appendAccessToken($data));
    //     return $this->parseResponseToJson($response);
    // }

    protected function appendAccessToken($data = [])
    {
        if (getenv("API_ACCESS_TOKEN")) {
            if (!array_key_exists("query", $data)) {
                $data["query"] = [];
            }

            $data["query"]["access_token"] = getenv("API_ACCESS_TOKEN");
        }

        return $data;
    }
}

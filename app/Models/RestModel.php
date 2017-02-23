<?php

namespace App\Models;

use GuzzleHttp\Client;
use Exception;
use ReflectionClass;

class RestModel
{
    public static function api()
    {
        $client = new Client([
            'base_uri' => getenv("TMT_API") ?
                getenv("TMT_API") :
                "http://localhost:3000/v1/"
        ]);
        return new static($client);
    }

    private $client;
    protected $hasMany = [];
    protected $belongsTo = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
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
        $response = $this->client->get($endpoint, $this->appendAccessToken($data));
        return $this->parseResponseToJson($response);
    }

    public function post($endpoint, $data = [])
    {
        $response = $this->client->post($endpoint, $this->appendAccessToken($data));
        return $this->parseResponseToJson($response);
    }

    public function patch($endpoint, $data = [])
    {
        $response = $this->client->patch($endpoint, $this->appendAccessToken($data));
        return $this->parseResponseToJson($response);
    }

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

    protected function getEntity()
    {
        $reflection = new ReflectionClass(static::class);
        $shortName = str_singular($reflection->getShortName());
        $fqn = sprintf("App\Models\Entities\%s", $shortName);

        if (class_exists($fqn)) {
            return new $fqn();
        }

        throw new Exception(sprintf("%s is not a valid class.", $fqn));
    }

    private function wrapEntity($data, $entity)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->hasMany)) {
                $Class = $this->hasMany[$key];
                $entity->{$key} = [];
                foreach ($value as $relation) {
                    $entity->{$key}[] = $this->wrapEntity($relation, new $Class());
                }
            } elseif (array_key_exists($key, $this->belongsTo)) {
                $Class = $this->belongsTo[$key];
                $entity->{$key} = $this->wrapEntity($value, new $Class());
            } else {
                $entity->{$key} = $value;
            }
        }
        return $entity;
    }

    private function parseResponseToJson($response)
    {
        $data = json_decode($response->getBody(), false);

        if (is_array($data)) {
            return $this->wrapInEntities($data);
        }

        return $this->wrapEntity($data);
    }

    private function wrapInEntities(array $data)
    {
        $resultset = [];

        foreach ($data as $row) {
            $resultset[] = $this->wrapEntity($row, $this->getEntity());
        }

        return $resultset;
    }
}

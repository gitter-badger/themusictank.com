<?php

namespace App\Models\Restful;

use Exception;
use ReflectionClass;

class ModelParser
{
    private $classContext;
    public $hasMany = [];
    public $belongsTo = [];

    public function parseResponse($response)
    {
        return $this->parseResponseToJson($response);
    }

    public function setContext($obj)
    {
        $this->classContext = get_class($obj);

        $this->hasMany = $obj->hasMany;
        $this->belongsTo = $obj->belongsTo;
    }

    public function formatException($response)
    {
        $data = json_decode($response->getBody(), true);

        if (is_array($data) && array_key_exists("error", $data)) {
            return new Exception($data["error"]["message"]);
        }

        return new Exception("Request to API has failed.");
    }

    protected function getEntity()
    {
        $reflection = new ReflectionClass($this->classContext);
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

        if (isset($data->id)) {
            return $this->wrapEntity($data, $this->getEntity());
        }

        return $data;
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

<?php

namespace App\Http\Controllers\Api;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends ApiController
{
    public function show($key)
    {
        return $this->answer(Configuration::where(['key' => $key])->first());
    }   
    
    public function update(Request $request, $key)
    {
        $configuration = Configuration::where(['key' => $key])->first();

        if (is_null($configuration)) {
            $configuration = new Configuration();
            $configuration->key = $key;
        }

        $configuration->value = $request->json("value");

        $this->answer([
            "status" => $configuration->save()
        ]);
    }
}

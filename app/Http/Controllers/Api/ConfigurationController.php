<?php

namespace App\Http\Controllers\Api;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends ApiController
{
    public function show($key)
    {
        return $this->answer(Configuration::where(['key' => $key])->firstOrFail());
    }

    public function update(Request $request, $key)
    {
        $configuration = Configuration::firstOrNew(['key' => $key]);
        $configuration->value = $request->json("value");
        $this->answer([
            "status" => $configuration->save()
        ]);
    }
}

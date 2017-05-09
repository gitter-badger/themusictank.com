<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class ApiController extends Controller
{
    public function index()
    {
        return $this->answer(null);
    }

    public function create() 
    {
        return $this->answer(null);
    }

    public function store() 
    {
        return $this->answer(null);
    }

    public function show($key) 
    {
        return $this->answer(null);
    }

    public function edit() 
    {
        return $this->answer(null);
    }

    public function update(Request $request, $key) 
    {
        return $this->answer(null);
    }

    public function destroy() 
    {
        return $this->answer(null);
    }

    protected function answer($data)
    {
        if (is_null($data)) {
            return [];
        }

        return $data;
    }

    protected function fail()
    {
        throw new Exception("Operation failed");
    }
}

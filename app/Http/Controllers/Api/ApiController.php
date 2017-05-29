<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use RespondsJson;

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
}

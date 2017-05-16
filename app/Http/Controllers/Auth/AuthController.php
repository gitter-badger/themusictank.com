<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        session(['_previous' => redirect()->back()]);
        return view('auth.index');
    }

    public function logout()
    {
        auth()->logout();
        session(['_previous' => null]);
        return redirect()->to('/');
    }
}

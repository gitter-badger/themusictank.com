<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        session(['_previous' => redirect()->back()]);
        return view('auth.index');
    }

    public function logout()
    {
        Auth::logout();
        session(['_previous' => null]);
        return redirect()->to('/');
    }
}

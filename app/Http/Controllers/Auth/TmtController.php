<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Socialite;

class TmtController extends Controller
{
    public function login()
    {
        session(['_previous' => redirect()->back()]);
        return view('auth.login');
    }
}

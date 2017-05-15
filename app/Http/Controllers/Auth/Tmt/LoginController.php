<?php

namespace App\Http\Controllers\Auth\Tmt;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/you';

    public function showLoginForm()
    {
        return view('auth.tmt.login');
    }
}

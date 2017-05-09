<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ApiSessionToken;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 *  This controller handles authenticating users for the application and
 *  redirecting them to your home screen. The controller uses a trait
 *  to conveniently provide its functionality to your applications.
*/
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/you/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function auth()
    {
        return view('auth.index');
    }
}

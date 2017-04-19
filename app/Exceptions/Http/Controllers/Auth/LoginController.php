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
    use AuthenticatesUsers {
        logout as protected authLogout;
        login as protected authLogin;
    }

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
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            ApiSessionToken::api()->logout(auth()->user()->getAuthIdentifier());
        } catch (Exception $e) {
            // Session was not deleted on API. TBD is that a real problem?
        }

        return $this->authLogout($request);
    }

    public function login(Request $request)
    {
        try {
            return $this->authLogin($request);
        } catch (Exception $e) {
            return $this->sendFailedLoginResponse($request);
        }
    }

}

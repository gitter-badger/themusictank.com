<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Profiles;

class ProfileController extends Controller
{
    public function auth()
    {
        return view('profiles.auth');
    }

    public function facebook()
    {
        return view('profiles.auth');
    }

    public function login()
    {
        return view('profiles.login');
    }

    public function dashboard()
    {
        return view('profiles.dashboard');
    }

    public function tmtlogin()
    {
        try {
            $request = request();
            $account = Profiles::api()->login(
                $request->input('email'),
                $request->input('password')
            );
            // start user session

            var_dump($account);

          //  return redirect()->action('ProfileController@dashboard');
        } catch (Exception $e) {
            return redirect()->action('ProfileController@login')
                ->withErrors([$e->getMessage()])
                ->with('email', $request->input('email'));
        }
    }

}

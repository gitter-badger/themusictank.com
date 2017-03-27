<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ApiSessionToken;
use App\Auth\User;
use Illuminate\Support\Facades\Auth;

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
        session(['_previous' => redirect()->back()]);
        return view('profiles.login');
    }

    public function dashboard()
    {
        return view('profiles.dashboard');
    }

    public function show($slug)
    {
        $profile = Profiles::api()->findBySlug($slug);

        if (!$profile) {
            return abort(404);
        }

        return view('profile.show', compact($profile));
    }

    public function create()
    {
        return view('profiles.create');
    }
}

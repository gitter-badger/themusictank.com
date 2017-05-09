<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('users.dashboard');
    }

    public function show($slug)
    {
        $user = User::whereSlug($slug)->firstOrFail();
        return view('users.show', compact('user'));
    }

    public function edit()
    {
        return view('users.edit');
    }

    public function create()
    {
        return view('users.create');
    }

    public function save()
    {
        // @todo
    }

    public function showCurve($slug, $trackSlug)
    {
        $profile = Profiles::api()->findBySlug($slug);
        $profile = Track::api()->findBySlug($slug);

        if (!$profile) {
            return abort(404);
        }

        return view('users.show', compact('users'));
    }
}

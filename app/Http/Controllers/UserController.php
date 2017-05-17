<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show($slug)
    {
        $user = User::whereSlug($slug)->firstOrFail();
        
        return view('users.show', compact('user'));
    }
}

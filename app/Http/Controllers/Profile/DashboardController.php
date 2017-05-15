<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{

    public function index()
    {
        return view('users.dashboard');
    }

}

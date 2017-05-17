<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Activity;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Activity::whereUserId($this->authUserId())
            ->orderBy('created_at', 'DESC')
            ->take(25)
            ->get();

        return view('notifications.index', compact('notifications'));
    }
}

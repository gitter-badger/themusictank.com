<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Activities;

class NotificationController extends Controller
{
    public function index()
    {
        $currentProfile = auth()->user()->getProfile();
        $notifications = Activities::api()->findRecent($currentProfile->id);
        return view('notifications.index', compact('notifications'));
    }
}

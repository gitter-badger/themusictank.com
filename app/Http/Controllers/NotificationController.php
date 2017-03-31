<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Notifications;

class NotificationController extends Controller
{
    public function index()
    {
        $currentProfile = auth()->user()->getProfile();
        $notifications = Notifications::api()->findRecent($currentProfile->id);
        return view('notifications.index', compact('notifications'));
    }
}

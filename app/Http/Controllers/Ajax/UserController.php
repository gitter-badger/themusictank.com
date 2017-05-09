<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class UserController extends Controller
{
    public function whatsUp()
    {
        $currentProfile = auth()->user()->getProfile();
        $timestamp = (int)request('timestamp');

        if ($timestamp > 0) {
            $dateTime = Carbon::createFromTimestamp($timestamp)->toDateTimeString();
            return response()->json((array)Activities::api()->findSince($dateTime, $currentProfile->id));
        }

        return response()->json((array)Activities::api()->findRecent($currentProfile->id, 5));
    }

    public function okstfu()
    {
        $currentProfile = auth()->user()->getProfile();
        return response()->json(
            Activities::api()
                ->markAsReadByIds(request('ids'), $currentProfile->id)
        );
    }

    public function bugreport()
    {

    }

}

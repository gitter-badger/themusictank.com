<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Carbon\Carbon;

class UserController extends Controller
{
    public function whatsUp()
    {
        $timestamp = (int)request('timestamp');
        $query = Activity::whereUserId(auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->take(6);

        if ($timestamp > 0) {
            $dateTime = Carbon::createFromTimestamp($timestamp)->toDateString();
            $query->whereDate('created_at', '>', $dateTime);
        }

        return response()->json($query->get());
    }

    public function okstfu()
    {
        $ids = (array)request('ids');
        $status = true;

        if (count($ids)) {
            $activities = Activity::whereUserId(auth()->user()->id)
                ->whereIn('id', $ids)
                ->get();

            foreach ($activities as $activity) {
                $activity->must_notify = 0;
                $status = $status && $activity->save();
            }
        }

        return response()->json(["status" => $status]);
    }

    public function bugreport()
    {

    }

}

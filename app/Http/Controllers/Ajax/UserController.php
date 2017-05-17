<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\UserSubscription;
use Carbon\Carbon;

class UserController extends Controller
{
    public function whatsUp()
    {
        $timestamp = (int)request('timestamp');
        $query = Activity::whereUserId($this->authUserId())
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

        if (count($ids)) {
            $activities = Activity::whereUserId(auth()->user()->id)
                ->whereIn('id', $ids)
                ->get();

            $status = true;
            foreach ($activities as $activity) {
                $activity->must_notify = 0;
                $status = $status && $activity->save();
            }
        }

        return response()->json([
            "status" => $status
        ]);
    }

    public function bugreport()
    {

    }

    public function follow()
    {
        $subscription = new UserSubscription();
        $subscription->user_id = $this->authUserId();
        $subscription->sub_id = (int)request('sub_id');
        $subscription->save();

        return response()->json($subscription);
    }

    public function unfollow()
    {
        $subscription = UserSubscription::whereUserId($this->authUserId())
            ->whereSubId((int)request('sub_id'))
            ->firstOrFail();

        return response()->json([
            "status" => $subscription->delete()
        ]);
    }

}

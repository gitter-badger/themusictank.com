<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\User;
use App\Models\TrackReview;
use App\Services\Curves\SubscriptionsCurve;

class TrackController extends Controller
{
    public function show($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        $globalCurve = $this->globalCurve($track);

        if ($this->hasActiveSession()) {
            $authUserCurve = $this->userCurve($track, auth()->user());
            $subscriptionsCurve = $this->subscriptionsCurve($track, auth()->user());
        }

        return view('tracks.show', compact('track', 'globalCurve', 'authUserCurve', 'subscriptionsCurve'));
    }

    public function viewUserReview($userSlug, $trackSlug)
    {
        $user = User::whereSlug($userSlug)->firstOrFail();
        $track = Track::whereSlug($trackSlug)->firstOrFail();

        $globalCurve = $this->globalCurve($track);
        $userCurve = $this->userCurve($track, $user);

        if ($this->hasActiveSession()) {
            $subscriptionsCurve = $this->subscriptionsCurve($track, auth()->user());
            if (!$this->isAuthUser($user)) {
                $authUserCurve = $this->userCurve($track, auth()->user());
            }
        }

        return view('tracks.show-review', compact('user', 'track', 'globalCurve', 'userCurve', 'authUserCurve', 'subscriptionsCurve'));
    }

    public function review($slug)
    {
        $track = Track::whereSlug($slug)->firstOrFail();
        return view('tracks.review', compact('track'));
    }

    private function globalCurve(Track $track)
    {
        return TrackReview::componentFields()
            ->forTrack($track)
            ->global()
            ->ordered()
            ->get();
    }

    private function userCurve(Track $track, User $user = null)
    {
        return TrackReview::componentFields()
            ->forTrack($track)
            ->forUser($user)
            ->ordered()
            ->get();
    }

    private function subscriptionsCurve(Track $track, User $user)
    {
        $curve = new SubscriptionsCurve($track, $user);
        return $curve->calculate();
    }
}

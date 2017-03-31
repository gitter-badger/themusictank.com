@php
    $profile = $notification->getLinkedObject();
@endphp
<h3>New follower</h3>
<p>
    <a href="{{ action('ProfileController@show', ['id' => $profile->slug]) }}">{{ $profile->name }}</a>
    is now following you!
</p>

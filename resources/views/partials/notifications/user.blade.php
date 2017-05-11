@if (!is_null($notification->associated_object))
<h3>New follower</h3>
<p>
    <a href="{{ action('UserController@show', ['id' => $notification->associated_object->slug]) }}">{{ $notification->associated_object->name }}</a>
    is now following you!
</p>
@endif

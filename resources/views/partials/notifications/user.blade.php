@if (!is_null($notification->associated_object))
<h3>New follower</h3>
<p>
    <a href="{{ route('user', ['id' => $notification->associated_object->slug]) }}">{{ $notification->associated_object->name }}</a>
    is now following you!
</p>
@endif

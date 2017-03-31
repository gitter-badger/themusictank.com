@extends('app')

@section('body-class', 'notifications index')

@section('content')
    <h1>Recent Notifications</h1>

    <section class="notifications">
        @if (count($notifications) > 0)

            <div class="heading">
                <button name="markAsRead" class="btn btn-primary">Mark all as read</button>
            </div>

            @foreach ($notifications as $notification)
                <div id="id-{{ $notification->id }}" class="notification {{ $notification->isViewed() ? "read" :  "new" }} {{ $notification->type }}">

                    <time datetime="{{ $notification->created_at }}" title="{{ $notification->created_at }}">
                        {{ $notification->getCreatedDateForHumans() }}
                    </time>

                    @if ($notification->hasLinkedObject())
                        @include('partials.notifications.' . $notification->getLinkedObjectType(), ['notification' => $notification])
                    @endif
                </div>
            @endforeach
        @else
            <p>You have no notifications for the moment.</p>
        @endif
    </div>
@endsection

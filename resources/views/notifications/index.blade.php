@extends('layouts.app')

@section('body-class', 'notifications index')

@section('content')
    <h1>Recent Notifications</h1>

    <section class="notifications">
        @if (count($notifications) > 0)
            @foreach ($notifications as $notification)
                <div class="notification {{ $notification->must_notify ? "read" :  "new" }} {{ $notification->associated_object_type_slug }}">

                    <time datetime="{{ $notification->getDateCreated() }}" title="{{ $notification->getDateCreated() }}">
                        {{ $notification->getCreatedDateForHumans() }}
                    </time>

                    @if (!is_null($notification->associated_object))
                        @include('partials.notifications.' . $notification->associated_object_type_slug, ['notification' => $notification])
                    @endif
                </div>
            @endforeach
        @else
            <p>You have no notifications for the moment.</p>
        @endif
    </section>
@endsection

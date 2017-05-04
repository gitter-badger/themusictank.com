@extends('app')

@section('body-class', 'notifications index')

@section('content')
    <h1>Recent Notifications</h1>

    <section class="notifications">
        @if (count($notifications) > 0)
            @foreach ($notifications as $notification)
                <div class="notification {{ $notification->isViewed() ? "read" :  "new" }} {{ $notification->getAssociationType() }}">

                    <time datetime="{{ $notification->getDateCreated() }}" title="{{ $notification->getDateCreated() }}">
                        {{ $notification->getCreatedDateForHumans() }}
                    </time>

                    @if ($notification->hasLinkedObject())
                        @include('partials.notifications.' . $notification->getAssociationType(), ['notification' => $notification])
                    @endif
                </div>
            @endforeach
        @else
            <p>You have no notifications for the moment.</p>
        @endif
    </section>
@endsection

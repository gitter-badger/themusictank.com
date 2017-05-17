@extends('layouts.app')

@section('title', sprintf('%s from %s by %s', $track->name, $track->album->name, $track->artist->name))
@section('og-title', sprintf('%s from %s by %s', $track->name, $track->album->name, $track->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, a song by %s.', $track->name, $track->artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s, a song by %s.', $track->name, $track->artist->name))
@section('body-class', 'tracks show')

@push('header')
<meta name="tmt:track:last_updated" content="{{ $track->updated_at }}">
<meta name="tmt:track:gid" content="{{ $track->gid }}">
@endpush

@section('backdrop')
    @include('partials.backdrop', ['entity' => $track->album])
@endsection

@push('app-javascript')
Tmt.app.reviewFrames([{
    @if (isset($globalCurve))        'global'       : <?php echo $globalCurve->toJson(); ?>,{{ PHP_EOL }}@endif
    @if (isset($subscriptionsCurve)) 'subscriptions': <?php echo $subscriptionsCurve->toJson(); ?>,{{ PHP_EOL }}@endif
    @if (isset($userCurve))          'user'         : <?php echo $userCurve->toJson(); ?>,{{ PHP_EOL }}@endif
    @if (isset($authUserCurve))      'auth_user'    : <?php echo $authUserCurve->toJson(); ?>,{{ PHP_EOL }}@endif
    'id' : {{ $track->id }}
}]);
@endpush

@section('content')
    @php
        $authUser = auth()->user();
    @endphp

    <section class="header">
        <h1>
            <a href="{{ route('artist', ['slug' => $track->artist->slug]) }}">
                {{ $track->artist->name }}
            </a>
        </h1>
        <h2>
            <a href="{{ route('album', ['slug' => $track->album->slug]) }}">
                {{ $track->album->name }}
            </a>
        </h2>
        <h3>
            <a href="{{ route('track', ['slug' => $track->slug]) }}">
                {{ $track->name }}
            </a>
            @include('partials.buttons.upvote', ['type' => "track", 'id' => $track->id])
        </h3>

        @if ($user->id === auth()->user()->id)
            <share-buttons
                user-slug="{{ $user->slug }}"
                song-name="{{ $track->name }}"
                song-slug="{{ $track->slug }}"
            ></share-buttons>
        @endif
    </section>

    <section class="review-cta">
        <a href="{{ route('review', ['slug' => $track->slug]) }}">Review track</a>
    </section>

    <section>
        @if (isset($authUserCurve))
            <line-chart object-id="{{ $track->id }}" datasource="auth_user"></line-chart>
        @endif

        @if (!is_null($subscriptionsCurve))
            <h3>Your subscriptions</h3>
            <line-chart object-id="{{ $track->id }}" datasource="subscriptions"></line-chart>
        @endif

        <h3>Global curve</h3>
        <line-chart object-id="{{ $track->id }}" datasource="global"></line-chart>

        @if (!is_null($userCurve))
            <h3>{{ $user->name }}'s review</h3>
            <line-chart object-id="{{ $track->id }}" datasource="user"></line-chart>
        @endif

        @include('partials.player', ['track' => $track])
    </section>

@endsection

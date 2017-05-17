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
    Tmt.app.reviewFrames(
        [{
            @if (isset($globalCurve))        'global'       : <?php echo $globalCurve->toJson(); ?>,{{ PHP_EOL }}@endif
            @if (isset($subscriptionsCurve)) 'subscriptions': <?php echo $subscriptionsCurve->toJson(); ?>,{{ PHP_EOL }}@endif
            @if (isset($authUserCurve))      'auth_user'    : <?php echo $authUserCurve->toJson(); ?>,{{ PHP_EOL }}@endif
            'id' : {{ $track->id }}
        }]
    );
@endpush


@section('content')
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
    </section>

    <section class="review-cta">
        <a href="{{ route('review', ['slug' => $track->slug]) }}">Review track</a>

        @if (isset($authUserCurve))
            You have already reviewed this track but you may review it again to fine tune your results.
            <a href="{{ route('user-review', ['slug' => auth()->user()->slug, 'trackSlug' => $track->slug]) }}">View your curve</a>
        @endif
    </section>

    <section>
        <line-chart object-id="{{ $track->id }}" datasource="subscriptions"></line-chart>
        <line-chart object-id="{{ $track->id }}" datasource="global"></line-chart>
        @include('partials.player', ['track' => $track])
    </section>


    <section class="track-navigation">
        @if ($track->previous()->count() && $previous = $track->previous()->first())
            <a href="{{ route('track', ['slug' => $previous->slug]) }}">
                <i class="fa fa-fast-backward" aria-hidden="true"></i> {{ $previous->name }}
            </a>
        @endif

        @if ($track->next()->count() && $next = $track->next()->first())
            <a href="{{ route('track', ['slug' => $next->slug]) }}">
                {{ $next->name }} <i class="fa fa-fast-forward" aria-hidden="true"></i>
            </a>
        @endif
    </section>

@endsection

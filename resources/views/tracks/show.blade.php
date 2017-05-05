@extends('app')

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
    @if ($globalCurves->count())

        Tmt.app.reviewFrames(
            [{
                'id' : {{ $track->id }},
                'global' : <?php echo $globalCurves->toJson(); ?>,
                'tanker' : [],
                'subscriptions' : []
            }]
        );
    @endif
@endpush


@section('content')
    <section class="header">
        <h1>
            <a href="{{ action('ArtistController@show', ['slug' => $track->artist->slug]) }}">
                {{ $track->artist->name }}
            </a>
        </h1>
        <h2>
            <a href="{{ action('AlbumController@show', ['slug' => $track->album->slug]) }}">
                {{ $track->album->name }}
            </a>
        </h2>
        <h3>
            <a href="{{ action('TrackController@show', ['slug' => $track->slug]) }}">
                {{ $track->name }}
            </a>
            @include('partials.buttons.upvote', ['type' => "track", 'id' => $track->id])
        </h3>
    </section>

    <a href="{{ action('TrackController@review', ['slug' => $track->slug]) }}">Review track</a>

    <line-chart object-id="{{ $track->id }}" datasource="global"></line-chart>

    @include('partials.player', ['track' => $track])

@endsection

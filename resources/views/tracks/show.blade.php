@extends('app')

@section('title', sprintf('%s from %s by %s', $track->name, $track->album->name, $track->artist->name))
@section('og-title', sprintf('%s from %s by %s', $track->name, $track->album->name, $track->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, a song by %s.', $track->name, $track->artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s, a song by %s.', $track->name, $track->artist->name))
@section('body-class', 'tracks show')

@push('header')
<meta name="tmt:track:last_updated" content="{{ $track->last_updated }}">
<meta name="tmt:track:gid" content="{{ $track->gid }}">
@endpush

@section('backdrop')
    @include('partials.backdrop', ['entity' => $track->album])
@endsection

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
    </h3>
</section>


@include('partials.buttons.upvote', ['type' => "track", 'id' => $track->id, 'artistid' => $track->artist->id])

@include('partials.player', ['track' => $track])

<section class="stats">
    Still to do.
</section>

@endsection

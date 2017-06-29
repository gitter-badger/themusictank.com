@extends('layouts.app')

@section('title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('body-class', 'albums show')

@push('header')
<meta name="tmt.album.last_updated" content="{{ $album->updated_at }}">
@endpush

<?php $sortedTracks = new TrackSort($album->tracks); ?>
@section('content')
    @component('components.cover-image', ['entity' => $album])
        <div class="title">
            <span>
                <a href="{{ route('artists') }}" class="subtitle">Artists</a>
            </span>
            <h1><a href="{{ route('album', ['slug' => $album->slug]) }}">{{ $album->name }}</a></h1>
            @include('components.vignettes.artist', ['artist' => $album->artist])
        </div>
        <div class="scores">
            @include('components.scores.pct', ['label' => "Global", 'percent' => $album->globalScore()])
            @if(!is_null(auth()->user()))
                @include('components.scores.pct', ['label' => "Subs", 'percent' => $album->subsScore(auth()->user())])
                @include('components.scores.pct', ['label' => "You", 'percent' => $album->score(auth()->user())])
            @endif
        </div>

        <a href="{{ route('review', ['slug' => $sortedTracks->first()->slug]) }}">Review album</a>
    @endcomponent

    <section class="tracklist">
        <h2>Tracklist</h2>

        @if (isset($album->tracks) && count($album->tracks))
            @foreach ($sortedTracks->all() as $albumName => $tracks)
                @if ($albumName !== "none")
                    <h3>{{ $albumName }}</h3>
                @endif

                <ul>
                @foreach ($tracks->all() as $track)
                    <li>
                        @include('components.buttons.upvote', ['type' => "track", 'id' => $track->id])
                        <a href="{{ route('track', ['slug' => $track->slug]) }}">
                            <em>{{ $track->getRealPosition() }}</em>
                            {{ $track->name }}
                        </a>
                    </li>
                @endforeach
                </ul>
            @endforeach
        @else
            <p>We have not found any tracks associated to {{ $album->name }}. The last time we found updates was {{ $album->getLastUpdatedForHumans() }}.</p>
        @endif
    </section>
@endsection

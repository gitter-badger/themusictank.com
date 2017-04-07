@extends('review')

@push('header')
<script src="//cdnjs.cloudflare.com/ajax/libs/p5.js/0.5.8/p5.js"></script>
@endpush

@section('body-class', 'tracks review')

@section('content')

    <section class="header">
        <h1>
            Reviewing
            <a href="{{ action('TrackController@show', ['slug' => $track->slug]) }}">
                {{ $track->name }}
            </a>
            <span>
                by
                <a href="{{ action('ArtistController@show', ['slug' => $track->artist->slug]) }}">
                    {{ $track->artist->name }}
                </a>
                from
                <a href="{{ action('AlbumController@show', ['slug' => $track->album->slug]) }}">
                    {{ $track->album->name }}
                </a>
            </span>
        </h1>
    </section>

    @include('partials.reviewer', ['track' => $track])


@endsection

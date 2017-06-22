@extends('layouts.app')

@section('title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('og-title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('body-class', 'albums show')

@push('header')
<meta name="tmt.album.last_updated" content="{{ $album->updated_at }}">

    @if (!is_null($album->hex))
        <style type="text/css">
            .cover-image-wrapper { background-color: {{ $album->hex }}; }
        </style>
    @endif
    @if ((bool)$album->artist->thumbnail)
        <style type="text/css">
            .artist-header {
                background-image: url({{ $album->artist->getThumbnailUrl("blur_mobile") }});
                @if (!is_null($album->artist->hex))
                    background-color: {{ $album->artist->hex }};
                @endif
            }
            @media (min-width: 501px) {
                .artist-header { background-image: url({{ $album->artist->getThumbnailUrl("blur") }}); }
            }
        </style>
    @endif
@endpush

@section('backdrop')
@endsection

@section('content')

<div class="artist-header">
    <div class="vignette">
        <a href="{{ route('artist', ['slug' => $album->artist->slug]) }}">
            <img src="{{ $album->artist->getThumbnailUrl() }}" alt="{{ $album->artist->name }}">
        </a>
    </div>
    <h2>{{ $album->artist->name }}</h2>
</div>

<div class="cover-image-wrapper">

    @include('partials.cover-image', ['entity' => $album])

    <div class="content">

        <h1>
            <a href="{{ route('album', ['slug' => $album->slug]) }}">
                {{ $album->name }}
            </a>
        </h1>

    <span>Released <time>{{ date("M", $album->month) }} {{ $album->day }} {{ $album->year }}</time></span>

    @include('partials.buttons.upvote', ['type' => "album", 'id' => $album->id])

    </div>

</div>


<section class="discography">
    <h2>Tracklist</h2>

    @if (isset($album->tracks) && count($album->tracks))
        <ul>
        @foreach ($album->tracks as $track)
            <li>
                @include('partials.buttons.upvote', ['type' => "track", 'id' => $track->id])

                <a href="{{ route('track', ['slug' => $track->slug]) }}">
                    <em>{{ $track->position }}</em>
                    {{ $track->name }}
                </a>
            </li>
        @endforeach
        </ul>
    @else
        <p>We have not found any tracks associated to {{ $album->name }}. The last time we found updates was {{ $album->getLastUpdatedForHumans() }}.</p>
    @endif
</section>
@endsection

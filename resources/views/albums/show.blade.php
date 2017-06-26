@extends('layouts.app')

@section('title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('og-title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('body-class', 'albums show')

@push('header')
<meta name="tmt.album.last_updated" content="{{ $album->updated_at }}">
@endpush

@section('content')
    @component('components.cover-image', ['entity' => $album])
        @include('components.vignettes.artist', ['artist' => $album->artist])
        @include('components.vignettes.album', ['album' => $album])

        <section class="tracklist">
            <h2>Tracklist</h2>

            @if (isset($album->tracks) && count($album->tracks))
                <ul>
                @foreach ($album->tracks as $track)
                    <li>
                        @include('components.buttons.upvote', ['type' => "track", 'id' => $track->id])

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
    @endcomponent
@endsection

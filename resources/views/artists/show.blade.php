@extends('layouts.app')

@section('title', sprintf('%s', $artist->name))
@section('og-title', sprintf('%s', $artist->name))
@section('description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('body-class', 'artists show')

@push('header')
<meta name="tmt.artist.last_updated" content="{{ $artist->updated_at }}">
@endpush

@section('content')

    @component('components.cover-image', ['entity' => $artist])
        @include('components.vignettes.artist', ['artist' => $artist])

        <section class="discography">
            @if ($artist->albums->count())
                @foreach ($artist->albums as $idx => $album)
                    @include('components.vignettes.album', ['album' => $album, 'score' => true])
                @endforeach
            @else
                <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
            @endif
        </section>
    @endcomponent

@endsection

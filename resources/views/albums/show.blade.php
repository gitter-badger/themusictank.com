@extends('app')

@section('title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('og-title', sprintf('%s by %s', $album->name, $album->artist->name))
@section('description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s, an album by %s.', $album->name, $album->artist->name))
@section('body-class', 'albums show')

@push('header')
<meta name="tmt.album.last_updated" content="{{ $album->last_updated }}">
<meta name="tmt.album.gid" content="{{ $album->gid }}">
@endpush

@section('backdrop')
    @include('partials.backdrop', ['entity' => $album])
@endsection

@section('content')
<section class="header">
    <h1>
        <a href="{{ action('ArtistController@show', ['slug' => $album->artist->slug]) }}">
            {{ $album->artist->name }}
        </a>
    </h1>
    <h2>
        <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
            {{ $album->name }}
        </a>
    </h2>
    <span>Released <time>{{ date("M", $album->month) }} {{ $album->day }} {{ $album->year }}</time></span>
</section>

<section class="discography">
    <h2>Discography</h2>

    @if (isset($album->tracks) && count($album->tracks))
        <ul>
        @foreach ($album->tracks as $track)
            <li>
                <a href="{{ action('TrackController@show', ['slug' => $track->slug]) }}">
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

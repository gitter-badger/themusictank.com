@extends('app')

@push('header')
<meta name="tmt.artist.last_updated" content="{{ $artist->last_updated }}">
<meta name="tmt.artist.gid" content="{{ $artist->gid }}">
@endpush

@section('body-class', 'artists show')

@section('backdrop')
    @include('partials.backdrop', ['entity' => $artist])
@endsection

@section('content')
<section class="header">
    <h1>
        <a href="{{ action('ArtistController@show', ['slug' => $artist->slug]) }}">
            {{ $artist->name }}
        </a>
    </h1>
</section>

<section class="discography">
    <h2>Discography</h2>

    @if (isset($artist->albums) && count($artist->albums))
        <ul>
        @foreach ($artist->albums as $album)
            <li>
                <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
                    <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
                </a>
                <h3>
                    <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
                        {{ $album->name }}
                    </a>
                </h3>
            </li>
        @endforeach
        </ul>
    @else
        <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
    @endif
</section>
@endsection

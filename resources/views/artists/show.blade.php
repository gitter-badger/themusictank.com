@extends('app')

@section('title', sprintf('%s', $artist->name))
@section('og-title', sprintf('%s', $artist->name))
@section('description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('body-class', 'artists show')

@push('header')
<meta name="tmt.artist.last_updated" content="{{ $artist->updated_at }}">
<meta name="tmt.artist.gid" content="{{ $artist->gid }}">
@endpush

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


    @if ($artist->albums->count())
    <ul>
        @foreach ($artist->albums->take(4) as $idx => $album)
            <li>
                <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
                    <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
                </a>
                <h3>
                    <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
                        {{ $album->name }}
                    </a>
                </h3>

                @include('partials.buttons.upvote', ['type' => "album", 'id' => $album->id])
            </li>
        @endforeach
        </ul>
    @else
        <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
    @endif
</section>
@endsection

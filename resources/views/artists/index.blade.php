@extends('app')

@section('body-class', 'artists index')

@section('backdrop')
    @if (isset($spotlightArtist))
        <section class="featured">
            @include('partials.backdrop', ['entity' => $spotlightArtist])

            <h1>
                <a href="{{ action('ArtistController@show', ['slug' => $spotlightArtist->slug]) }}">
                    {{ $spotlightArtist->name }}
                </a>
            </h1>

            @if (isset($spotlightArtist->albums) && count($spotlightArtist->albums))
                <ul>
                    @foreach ($spotlightArtist->albums as $idx => $album)
                        @if ($idx < 3)
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
                        @else
                            <li>
                                <a href="{{ action('ArtistController@show', ['slug' => $spotlightArtist->slug]) }}">More</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
@endsection

@section('content')
    @if (isset($featuredArtists) && count($featuredArtists))
        <section class="featured-artists">
            <ul>
                @foreach ($featuredArtists as $artist)
                    <a href="{{ action('ArtistController@show', ['slug' => $artist->slug]) }}">
                        <img src="{{ $artist->getThumbnailUrl() }}" alt="{{ $artist->name }}" title="{{ $artist->name }}">
                    </a>
                    <h3>
                        <a href="{{ action('ArtistController@show', ['slug' => $artist->slug]) }}">
                            {{ $artist->name }}
                        </a>
                    </h3>
                @endforeach
            </ul>
        </section>
    @endif
@endsection

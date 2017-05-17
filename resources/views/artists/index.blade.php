@extends('layouts.app')

@section('body-class', 'artists index')

@section('backdrop')
    @if (isset($spotlightArtist))
        <section class="featured">
            @include('partials.backdrop', ['entity' => $spotlightArtist])

            <h1>
                <a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">
                    {{ $spotlightArtist->name }}
                </a>
            </h1>

            @if ($spotlightArtist->albums->count())
                <ul>
                    @foreach ($spotlightArtist->albums->take(4) as $idx => $album)
                        @if ($idx < 3)
                            <li>
                                <a href="{{ route('album', ['slug' => $album->slug]) }}">
                                    <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
                                </a>
                                <h3>
                                    <a href="{{ route('album', ['slug' => $album->slug]) }}">
                                        {{ $album->name }}
                                    </a>
                                </h3>
                                @include('partials.buttons.upvote', ['type' => "album", 'id' => $album->id])
                            </li>
                        @else
                            <li class="more">
                                <a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">More</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
@endsection

@section('content')
    @if (isset($featuredArtists) && $featuredArtists->count())
        <section class="featured-artists">
            <ul>
                @foreach ($featuredArtists as $artist)
                    <a href="{{ route('artist', ['slug' => $artist->slug]) }}">
                        <img src="{{ $artist->getThumbnailUrl() }}" alt="{{ $artist->name }}" title="{{ $artist->name }}">
                    </a>
                    <h3>
                        <a href="{{ route('artist', ['slug' => $artist->slug]) }}">
                            {{ $artist->name }}
                        </a>
                    </h3>
                @endforeach
            </ul>
        </section>
    @endif
@endsection

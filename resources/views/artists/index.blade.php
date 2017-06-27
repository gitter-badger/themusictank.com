@extends('layouts.app')
@section('body-class', 'artists index')
@section('content')
    @if (isset($spotlightArtist))
        @component('components.cover-image', ['entity' => $spotlightArtist])
            <article class="featured">

                <div class="title">
                    <span class="subtitle">Featured Artist</span>
                    <h1><a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">{{ $spotlightArtist->name }}</a></h1>
                </div>

                @if ($spotlightArtist->albums->count())
                    <section class="discography clear">
                        @foreach ($spotlightArtist->albums->take(4) as $idx => $album)
                            @if ($idx < 3)
                                @include('components.vignettes.album', ['album' => $album, 'score' => true])
                            @else
                                <div class="more">
                                    <a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">
                                        And {{ $spotlightArtist->albums->count() - 3 }} more
                                        <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </section>
                @endif
            </article>
        @endcomponent
    @endif

    @if (isset($featuredArtists) && $featuredArtists->count())
        <section class="featured-artists">
            @foreach ($featuredArtists as $artist)
                @include('components.vignettes.artist', ['artist' => $artist])
            @endforeach
        </section>
    @endif
@endsection

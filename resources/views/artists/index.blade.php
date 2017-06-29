@extends('layouts.app')
@section('title', 'Featured artists')
@section('description', 'View the most popular artists on The Music Tank at the moment.')

@section('body-class', 'artists index')

@section('content')
    @if (isset($spotlightArtist))
        @component('components.cover-image', ['entity' => $spotlightArtist])
            <article class="featured">

                <div class="titles">
                    <span class="subtitle">Featured Artist</span>
                    <h1><a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">{{ $spotlightArtist->name }}</a></h1>
                </div>

                @if ($spotlightArtist->albums->count())
                    <section class="discography container clear">
                        @foreach ($spotlightArtist->albums->take(4) as $idx => $album)
                            @if ($idx < 3)
                                @include('components.vignettes.album', ['album' => $album, 'score' => true])
                            @else
                                <div class="more">
                                    <a href="{{ route('artist', ['slug' => $spotlightArtist->slug]) }}">
                                        {{ $spotlightArtist->albums->count() - 3 }} more
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
        <section class="featured-artists content">
            <h2>More Popular Artists</h2>
            @php
                $source = $featuredArtists->all();
                $lastTake = 0;
            @endphp
            @while(count($source))
                <div class="container">
                    @php
                        $lastTake = ($lastTake == 4) ? 5 : 4;
                    @endphp
                    @foreach (array_splice($source, 0, $lastTake) as $artist)
                        @include('components.vignettes.artist', ['artist' => $artist])
                    @endforeach
                </div>
            @endwhile
        </section>
    @endif
@endsection

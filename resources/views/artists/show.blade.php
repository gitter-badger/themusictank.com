@extends('layouts.app')

@section('title', sprintf('%s', $artist->name))
@section('og-title', sprintf('%s', $artist->name))
@section('description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('body-class', 'artists show')

@push('header')
<meta name="tmt.artist.last_updated" content="{{ $artist->updated_at }}">
@if (!is_null($artist->hex))<style type="text/css">.cover-image-wrapper { background-color: {{ $artist->hex }}; }</style>@endif
@endpush

@section('backdrop')
@endsection

@section('content')

    <div class="cover-image-wrapper">

        @include('partials.cover-image', ['entity' => $artist])

        <div class="content">

            @include('partials.vignettes.artist', ['artist' => $artist])


            <section class="discography">
                @if ($artist->albums->count())
                    @foreach ($artist->albums->take(4) as $idx => $album)
                        <div class="album">
                            <div class="thumbnail">
                                <a href="{{ route('album', ['slug' => $album->slug]) }}">
                                    <img src="{{ $album->getThumbnailUrl() }}" alt="{{ $album->name }}" title="{{ $album->name }}">
                                </a>
                            </div>
                            <div class="text">
                                <h2>
                                    <a href="{{ route('album', ['slug' => $album->slug]) }}">
                                        {{ $album->name }}
                                    </a>
                                </h2>
                                @include('partials.buttons.upvote', ['type' => "album", 'id' => $album->id])
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
                @endif
            </section>

        </div>
    </div>

@endsection

@extends('layouts.app')

@section('title', sprintf('%s', $artist->name))
@section('og-title', sprintf('%s', $artist->name))
@section('description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('og-description', sprintf('View the reviewing statistics of %s.', $artist->name))
@section('body-class', 'artists show')

@push('header')
<meta name="tmt.artist.last_updated" content="{{ $artist->updated_at }}">
<meta name="tmt.artist.gid" content="{{ $artist->gid }}">

@if (!is_null($artist->hex))<style type="text/css">.cover-image-wrapper { background-color: {{ $artist->hex }}; }</style>@endif
@endpush

@section('backdrop')
@endsection

@section('content')

    <div class="cover-image-wrapper">

        @include('partials.cover-image', ['entity' => $artist])

        <div class="content">

            <h1>
                <a href="{{ route('artist', ['slug' => $artist->slug]) }}">
                    {{ $artist->name }}
                </a>
            </h1>

            <section class="discography">
                <h2>Discography</h2>


                @if ($artist->albums->count())
                <ul>
                    @foreach ($artist->albums->take(4) as $idx => $album)
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
                    @endforeach
                    </ul>
                @else
                    <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
                @endif
            </section>

        </div>
    </div>

@endsection

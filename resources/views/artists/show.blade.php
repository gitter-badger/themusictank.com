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
        <div class="title">
            <h1><a href="{{ route('artist', ['slug' => $artist->slug]) }}">{{ $artist->name }}</a></h1>
        </div>
        <div class="scores">
            @include('components.scores.percent', ['label' => "Global", 'percent' => $artist->globalScore()])
            @include('components.scores.percent', ['label' => "Global", 'percent' => $artist->globalScore()])
            @if(!is_null(auth()->user()))
                @include('components.scores.percent', ['label' => "Subs", 'percent' => $artist->subsScore(auth()->user())])
            @endif
        </div>
    @endcomponent

    <section class="discography clear">
        @if ($artist->albums->count())
            @foreach ($artist->albums as $idx => $album)
                @include('components.vignettes.album', ['album' => $album])
            @endforeach
        @else
            <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
        @endif
    </section>

@endsection

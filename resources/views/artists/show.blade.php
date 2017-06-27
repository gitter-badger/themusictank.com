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
            <a href="{{ route('artists') }}" class="subtitle">Artists</a>
            <h1><a href="{{ route('artist', ['slug' => $artist->slug]) }}">{{ $artist->name }}</a></h1>
        </div>
        <div class="scores">
            @include('components.scores.pct', ['label' => "Global", 'percent' => $artist->globalScore()])
            @if(!is_null(auth()->user()))
                @include('components.scores.pct', ['label' => "Subs", 'percent' => $artist->subsScore(auth()->user())])
            @endif
        </div>
    @endcomponent

    <section class="discography">
        @if ($artist->albums->count())
            @foreach ($artist->albums as $idx => $album)
                @include('components.album-details', ['album' => $album])
            @endforeach
        @else
            <p>We have not found any releases belonging to {{ $artist->name }}. The last time we found updates was {{ $artist->getLastUpdatedForHumans() }}.</p>
        @endif
    </section>
@endsection

@push('app-javascript')
Tmt.app.reviewFrames([
    @foreach ($artist->albums as $idx => $album)
    {
        @if (isset($globalCurve))        'global'       : <?php echo $globalCurve->toJson(); ?>,{{ PHP_EOL }}@endif
        @if (isset($subscriptionsCurve)) 'subscriptions': <?php echo $subscriptionsCurve->toJson(); ?>,{{ PHP_EOL }}@endif
        @if (isset($authUserCurve))      'auth_user'    : <?php echo $authUserCurve->toJson(); ?>,{{ PHP_EOL }}@endif
        'id' : {{ $album->id }}
    },
    @endforeach
    {}
]);
@endpush

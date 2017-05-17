@extends('layouts.app')

@section('body-class', 'achievements index')

@section('content')

    <h1>Achievements</h1>

    @if (isset($rareAchievements))
        <h2>Rarest</h2>
        <p>This is the list of the rarest achievements obtained accros the community. Did you unlock one of them?</p>
        <ul>
        @foreach ($rareAchievements as $achievement)
            <li><em>{{ $achievement->pct }} %</em> <a href="{{ route('achievement', ['slug' => $achievement->slug]) }}">{{ $achievement->name }}</a></li>
        @endforeach
        </ul>
    @endif

    @if (isset($userAchievements))
        <h2>Your recent achievements</h2>
        <ul>
            @foreach ($userAchievements as $userAchievement)
                <li><a href="{{ route('achievement', ['slug' => $userAchievement->achievement->slug]) }}">{{ $userAchievement->achievement->name }}</a></li>
            @endforeach
        </ul>
        <a href="{{ route('user-achievements', ['slug' => auth()->user()->slug]) }}">More</a>
    @endif

    <a href="{{ route('achievement-list') }}">View all achievements</a>

@endsection

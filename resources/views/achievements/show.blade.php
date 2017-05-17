@extends('layouts.app')

@section('body-class', 'achievements index')

@section('content')
    <h1><a href="{{ route('achievement-list') }}">Achievements</a></h1>
    <h2><a href="{{ route('achievement', ['slug' => $achievement->slug]) }}">{{ $achievement->name }}</a></h2>

    <p>{{ $achievement->description }}</p>

    <p>{{ $popularity }} % of tankers have unlocked this achievement.</p>

    @if (isset($authUserHasIt) && $authUserHasIt)
        <p>
            You have unlocked this achievement on
            <time datetime="{{ $authUserHasIt->getDateCreated() }}" title="{{ $authUserHasIt->getDateCreated() }}">
                {{ $authUserHasIt->getCreatedDateForHumans() }}
            </time>
        </p>
    @endif
@endsection

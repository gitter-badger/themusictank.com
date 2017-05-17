@extends('layouts.app')

@section('body-class', 'achievements index')

@section('content')

    <h1>Achievements</h1>
    <h2>Full listing</h2>

    <p>This is the list of achievements that can be obtained on the website</p>
    <ul>
    @foreach ((array)$achievements as $achievement)
        <li><a href="{{ route('achievement', ['slug' => $achievement->slug]) }}">{{ $achievement->name }}</a></li>
    @endforeach
    </ul>

@endsection

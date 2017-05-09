@extends('app')
@section('body-class', 'profiles show')

@section('content')
<section class="header">

    @if ($user->thumbnail)
        <a href="{{ action('UserController@show', ['slug' => $user->slug]) }}">
            <img src="{{ $user->thumbnail }}" alt="{{ $user->name }}" title="{{ $user->name }}">
        </a>
    @endif
    <h1>    
        <a href="{{ action('UserController@show', ['slug' => $user->slug]) }}">
            {{ $user->name }}
        </a>
    </h1>
</section>

<section>
    Still todo.
</section>

@endsection

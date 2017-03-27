@extends('app')
@section('body-class', 'profiles show')

@section('content')
<section class="header">
    <h1>
        <a href="{{ action('ProfileController@show', ['slug' => $profile->slug]) }}">
            {{ $profile->username }}
        </a>
    </h1>
    <h2>{{ $profile->name }}</h2>
</section>

<section>
    Still todo.
</section>

@endsection

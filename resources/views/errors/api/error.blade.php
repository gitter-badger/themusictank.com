@extends('app')

@section('body-class', 'error apidown')

@section('content')
    <article>
        <h2>API error</h2>

        <p class="lead">
            There is something wrong with the last request the website did to the API.
        </p>

        @if (isset($exception))
            <p>It replied with:</p>
            <blockquote>"{{ json_decode($exception->getResponse()->getBody(true))->message }}"</blockquote>
        @endif

        <p>
            Someone is likely already panicking trying to bring it back online, but may
            <a href="https://github.com/themusictank/themusictank/issues">file a new incident report</a>
            anyway.
        </p>
    </article>
@endsection

@extends('app')

@section('body-class', 'error apidown')

@section('content')
    <article>
        <h2>The API is down</h2>

        <p class="lead">
            We can't connect to the API and get the information required to
            run the website.
        </p>

        <p>
            Someone is likely already panicking trying to bring it back online, but may
            <a href="https://github.com/themusictank/api/issues">file a new incident report</a>
            anyway.
        </p>
    </article>
@endsection

@extends('app')

@section('body-class', 'profiles login')


@section('content')

    <h1>Tanker Profiles</h1>
    <h2>Access your account</h2>

    @if (isset($error))
        <p class="error">{{ $error }}</p>
    @endif

    <section>
        <h3>Authentication</h3>
        <ul>
            <li>
                <a href="{{ action('ProfileController@facebook') }}">
                    <i class="fa fa-facebook-square"></i> Login with Facebook
                </a>
            </li>
            <li>
                <a href="{{ action('ProfileController@login') }}">
                    <i class="fa fa-facebook-square"></i> Login with TMT account
                </a>
            </li>
            <li>
                <a href="{{ action('ProfileController@create') }}">
                    Create a new TMT account
                </a>
            </li>
        </ul>
    </section>

@endsection

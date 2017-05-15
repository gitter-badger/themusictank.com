@extends('app')

@section('body-class', 'profiles auth')

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
                <a href="{{ action('Auth\SocialController@facebookRedirect') }}">
                    <i class="fa fa-facebook-square" aria-hidden="true"></i> Login with Facebook
                </a>
            </li>
            <li>
                <a href="{{ action('Auth\TmtController@login') }}">
                    <i class="fa fa-user-circle" aria-hidden="true"></i> Login with a TMT account
                </a>
            </li>
            <li>
                <a href="{{ action('Auth\TmtController@create') }}">
                   <i class="fa fa-address-card-o" aria-hidden="true"></i> Create a new TMT account
                </a>
            </li>
        </ul>
    </section>

@endsection

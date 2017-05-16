@extends('app')

@section('body-class', 'profiles auth')

@section('content')

    <h1>Tanker Profiles</h1>
    <h2>Access your account</h2>

    @include('partials.application-messages')

    <section>
        <h3>Authentication</h3>
        <ul>
            <li>
                <a href="{{ action('Auth\Social\FacebookController@redirect') }}">
                    <i class="fa fa-facebook-square" aria-hidden="true"></i> Login with Facebook
                </a>
            </li>
            <li>
                <a href="{{ action('Auth\Tmt\LoginController@showLoginForm') }}">
                    <i class="fa fa-user-circle" aria-hidden="true"></i> Login with a TMT account
                </a>
            </li>
            <li>
                <a href="{{ action('Auth\Tmt\RegisterController@showRegisterForm') }}">
                   <i class="fa fa-address-card-o" aria-hidden="true"></i> Create a new TMT account
                </a>
            </li>
        </ul>
    </section>

@endsection

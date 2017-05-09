@extends('app')

@section('body-class', 'profiles login tmt-login')

@section('content')
    <a href="{{ action('ProfileController@auth') }}">Log in using another method</a>

    <h1>Login with a TMT account</h1>

    @include('partials.errors')

    {{ Form::open(['action' => "Auth\LoginController@login"]) }}
        {{ Form::hidden('remember', true) }}
        <fieldset>
            {{ Form::label('email', 'E-Mail Address') }}
            {{ Form::email('email', Session::get('email')) }}
        </fieldset>
        <fieldset>
            {{ Form::label('password', 'Password') }}
            {{ Form::password('password') }}
        </fieldset>
        {{ Form::submit('Login') }}
    {{ Form::close() }}

@endsection

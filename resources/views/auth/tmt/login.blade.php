@extends('app')

@section('body-class', 'profiles login tmt-login')

@section('content')
    <a href="{{ action('Auth\AuthController@index') }}">Log in using another method</a>

    <h1>Login with a TMT account</h1>

    @include('partials.errors')

    {{ Form::open(['action' => "Auth\Tmt\LoginController@login"]) }}
        <fieldset>
            {{ Form::label('email', 'E-Mail Address') }}
            {{ Form::email('email', Session::get('email')) }}
        </fieldset>
        <fieldset>
            {{ Form::label('password', 'Password') }}
            {{ Form::password('password') }}
        </fieldset>
        <fieldset>
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
        </fieldset>
        {{ Form::submit('Login') }}
    {{ Form::close() }}

@endsection

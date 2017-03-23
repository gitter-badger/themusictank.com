@extends('app')

@section('body-class', 'profiles login tmt-login')

@section('content')
    <a href="{{ action('ProfileController@auth') }}">Log in using another method</a>

    <h1>Create a TMT account</h1>

    @include('partials.errors')

    {{ Form::open(['action' => "ProfileController@create"]) }}
        <fieldset>
            {{ Form::label('email', 'E-Mail Address') }}
            {{ Form::email('email', Session::get('email')) }}

            {{ Form::label('firstname', 'First name') }}
            {{ Form::email('firstname', Session::get('firstname')) }}

            {{ Form::label('lastname', 'Last name') }}
            {{ Form::email('lastname', Session::get('lastname')) }}
        </fieldset>
        <fieldset>
            {{ Form::label('password', 'Password') }}
            {{ Form::password('password') }}

            {{ Form::label('password_confirm', 'Password confirmation') }}
            {{ Form::password('password_confirm') }}
        </fieldset>
        {{ Form::submit('Create') }}
    {{ Form::close() }}

@endsection

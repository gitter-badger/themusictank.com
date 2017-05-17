@extends('layouts.app')

@section('body-class', 'profiles login tmt-login')

@section('content')

    <a href="{{ route('login') }}">Log in using another method</a>

    <h1>Register</h1>
    <h2>Create a TMT account</h2>

    @include('partials.application-messages')

    {{ Form::open([ 'route' => 'tmt-register-do']) }}
        <fieldset class="{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @if ($errors->has('name'))
                <p class="help-block">{{ $errors->first('name') }}</p>
            @endif
        </fieldset>

        <fieldset class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">E-Mail Address</label>
            <input id="email" type="email"name="email" value="{{ old('email') }}" required>
            @if ($errors->has('email'))
                <p class="help-block">{{ $errors->first('email') }}</p>
            @endif
        </fieldset>

        <fieldset class="{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            @if ($errors->has('password'))
                <p class="help-block">{{ $errors->first('password') }}</p>
            @endif
        </fieldset>

        <fieldset>
            <label for="password-confirm">Confirm Password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required>
        </fieldset>

        <fieldset>
            <button type="submit" class="btn btn-primary">Register</button>
        </fieldset>
    </form>
@endsection

@extends('layouts.app')

@section('body-class', 'profiles login tmt-login')

@section('content')
    <a href="{{ route('login') }}">Log in using another method</a>

    <h1>Login</h1>
    <h2>Tanker Account</h2>

    @include('partials.application-messages')

    {{ Form::open(['route' => 'tmt-login-do']) }}
        <fieldset class="{{ $errors->has('email') ? ' has-error' : '' }}">
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
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
        </fieldset>
        <fieldset>
            <button type="submit" class="btn btn-primary">Login</button>
        </fieldset>
    </form>

@endsection

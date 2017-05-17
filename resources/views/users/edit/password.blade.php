@extends('layouts.app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>Change your password</h2>

    <p>You can set a password even if you use a third party login like Facebook. This will allow you to also login using your Tanker profile with your email and password.</p>
    <p>This is a required step if you wish to disconnect all 3rd party integrations.</p>

    @include('partials.application-messages')

    {!! Form::model($user, ['route' => 'profile-password-save']) !!}
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
            <button type="submit" class="btn btn-primary">Update</button>
        </fieldset>
    </form>

@endsection

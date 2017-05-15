@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>Change your password</h2>

    <form role="form" method="POST" action="{{ action('Profile\ManageController@passwordPost') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password">Password</label>
            <input type="password" name="password" required>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Update
        </button>
    </form>

@endsection

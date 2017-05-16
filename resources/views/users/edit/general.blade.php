@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>General</h2>

    <p>Though most of these fields are optional, filling them in does make your profile more lively.</p>

    @include('partials.application-messages')

     {!! Form::model($user, ['action' => 'Profile\ManageController@saveGeneral']) !!}
        <fieldset class="{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus>
            @if ($errors->has('name'))
                <p class="help-block">{{ $errors->first('name') }}</p>
            @endif
        </fieldset>

        <fieldset class="{{ $errors->has('slug') ? ' has-error' : '' }}">
            <label for="slug">Vanity URL (your unique url slug)</label>
            <input id="slug" type="text" name="slug" value="{{ old('slug', $user->slug) }}" required>
            @if ($errors->has('slug'))
                <p class="help-block">{{ $errors->first('slug') }}</p>
            @endif
        </fieldset>

        @if ($user->socialAccounts->count() > 0)
            <fieldset>
                <label>E-Mail Address</label>
                <span class="input readonly">{!! $user->email !!}</span>
                <p class="info">
                    Because you have linked your TMT account to another login service (ex: you logged in with Facebook)
                    we can't allow you to modify your email address. It's what we use to keep the association running.
                </p>
                <p class="info">If you absolutely wish to change your email, then <a href="{{ action('Profile\ManageController@thirdparty') }}">unlink all third party services</a> first.</p>
            </fieldset>
        @else
            <fieldset class="{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">E-Mail Address</label>
                <input id="email" type="email"name="email" value="{{ old('email', $user->email) }}" required>
                @if ($errors->has('email'))
                    <p class="help-block">{{ $errors->first('email') }}</p>
                @endif
            </fieldset>
        @endif

        <fieldset>
            <button type="submit" class="btn btn-primary">Update</button>
        </fieldseT>
    </form>

@endsection

@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>General</h2>

     {!! Form::model($user, ['action' => 'Profile\ManageController@generalPost']) !!}

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
            <label for="slug">Vanity URL (your unique url slug)</label>
                <input type="text" name="slug" value="{{ old('slug', $user->slug) }}" required>
                @if ($errors->has('slug'))
                    <span class="help-block">
                        <strong>{{ $errors->first('slug') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        @if ($user->socialAccounts->count() > 0)
            <p class="info">
                Because you have linked your TMT account with another login service (ex: you logged in with Facebook)
                we can't allow you to update your email address.
            </p>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" readonly>
        @else
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">E-Mail Address</label>
                <input type="email"name="email" value="{{ old('email', $user->email) }}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        @endif

        <button type="submit" class="btn btn-primary">
            Update
        </button>

    </form>

@endsection

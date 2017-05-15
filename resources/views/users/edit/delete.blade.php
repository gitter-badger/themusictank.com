@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>Delete your account</h2>

    <p>Deleting your TMT account will delete all content you have generated on the website. This is a destructive action that is not reversible.</p>

    <form role="form" method="POST" action="{{ action('Profile\ManageController@deletePost') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('confirm') ? ' has-error' : '' }}">
            <label for="confirm">If you wish to delete your account on TMT, type "delete my account" in the following box</label>
                <input type="text" name="confirm" value="{{ old('confirm') }}" required autofocus>
                @if ($errors->has('confirm'))
                    <span class="help-block">
                        <strong>{{ $errors->first('confirm') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('anonymous_frames') ? ' has-error' : '' }}">
            <label for="anonymous_frames">Though we will delete all your personal data, can we still use your reviewing activity anonymously?</label>
                <input type="checkbox" name="anonymous_frames" value="{{ old('anonymous_frames') }}" checked>
                Sure why not?
                @if ($errors->has('anonymous_frames'))
                    <span class="help-block">
                        <strong>{{ $errors->first('anonymous_frames') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Delete my account
        </button>
    </form>

@endsection

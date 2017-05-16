@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    @include('partials.user-nav')

    <h1>Settings</h1>
    <h2>Delete your account</h2>

    <p>Deleting your TMT account will delete all content you have generated on the website. This is a <strong>destructive action that is not reversible</strong>.</p>

    @include('partials.application-messages')

    {!! Form::model(auth()->user(), ['action' => 'Profile\ManageController@saveDelete']) !!}
        <fieldset class="{{ $errors->has('confirm') ? ' has-error' : '' }}">
            <label for="confirm">If you wish to delete your account on TMT, type "delete my account" in the following box</label>
            <input id="confirm" type="text" name="confirm" value="{{ old('confirm') }}" required autofocus>
            @if ($errors->has('confirm'))
                <p class="help-block">{{ $errors->first('confirm') }}</p>
            @endif
        </fieldset>

        <fieldset class="{{ $errors->has('anonymous_frames') ? ' has-error' : '' }}">
            <label for="anonymous_frames_1">Though we will delete all your personal data, can we please still use your reviewing activity if we make it anonymous?</label>
            <ul class="ctrl-list">
                <li><label><input id="anonymous_frames_1" type="radio" name="anonymous_frames" value="1" checked>Sure</label></li>
                <li><label><input id="anonymous_frames_2" type="radio" name="anonymous_frames" value="0">I'd rather not</label></li>
            <ul>
        </fieldset>

        <fieldset>
            <button type="submit" class="btn btn-primary">Delete my account</button>
        </fieldset>
    </form>

@endsection

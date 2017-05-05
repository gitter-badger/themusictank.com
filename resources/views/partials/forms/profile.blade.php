

@php
    $isNewProfile = is_null($profile) || (int)$profile->id === 0;
@endphp

@include('partials.errors')

{{ Form::open(['action' => "ProfileController@save"]) }}
    @if (!$isNewProfile)
        {{ Form::hidden('id', $profile->id) }}
    @endif

    <fieldset>
        {{ Form::label('email', 'E-Mail Address') }}
        {{ Form::email('email', Session::get('email') ? Session::get('email') : $profile->email) }}

        {{ Form::label('username', 'Username') }}
        {{ Form::email('username', Session::get('username') ? Session::get('username') : $profile->username) }}
        <p>We will use your username to generate your custom URLs on the website.</p>

        {{ Form::label('name', 'Name') }}
        {{ Form::email('name', Session::get('name') ? Session::get('name') : $profile->name) }}
        <p>If you don't want to set a name, we will fallback to using your username.</p>
    </fieldset>


    @if (!$isNewProfile)
        <visible-toggler target="form fieldset.update-pw" label="Update password"></visible-toggler>
    @endif

    <fieldset class="update-pw">
        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}

        {{ Form::label('password_confirm', 'Password confirmation') }}
        {{ Form::password('password_confirm') }}
    </fieldset>

    {{ Form::submit($isNewProfile ? 'Create' : 'Save') }}
{{ Form::close() }}

@extends('app')

@section('body-class', 'profiles settings thirdparty')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
        $setAccounts = [];
    @endphp

    <h1>Settings</h1>
    <h2>Third party integration</h2>

    @if (!isset($user->password))
        <p class="warning">You may not revoke associations for the time being as no password has been set to your account.</p>
    @endif

    @include('partials.application-messages')

    <ul>
    @if ($user->socialAccounts->count() > 0)
        @foreach ($user->socialAccounts as $account)
            @php
                $setAccounts[] = $account->provider;
            @endphp
            <li>
                <strong>{{ ucfirst($account->provider) }}</strong> was associated
                <time datetime="{{ $account->getDateCreated() }}" title="{{ $account->getDateCreated() }}">
                    {{ $account->getCreatedDateForHumans() }}
                </time>

                @if (isset($user->password))
                    {!! Form::model($user, ['action' => 'Profile\ManageController@revokeThirdParty']) !!}
                        <input type="hidden" name="id" value="{{ $account->id }}">
                        <button type="submit">Revoke</button>
                    </form>
                @endif
            </li>
        @endforeach
    @else
        <li>Your account is not associated to a third party app.</li>
    @endif
    </ul>


    @if ($user->socialAccounts->count() < 1)
    <h2>Add association</h2>
    <p>
        If you use <em>{!! $user->email !!}</em> as email for the following services, you may link them with your
        Tanker account.
    </p>
    <ul>
        @if (!in_array('facebook', $setAccounts))
            <li>
                <a href="{{ action('Auth\Social\FacebookController@redirect') }}">
                    <i class="fa fa-facebook-square" aria-hidden="true"></i> Login with Facebook
                </a>
            </li>
        @endif
    </ul>
    @endif

@endsection

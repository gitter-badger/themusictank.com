@extends('app')

@section('body-class', 'profiles settings thirdparty')

@section('content')

    @include('partials.user-nav')

    @php
        $user = auth()->user();
    @endphp

    <h1>Settings</h1>
    <h2>Third party integration</h2>

    <ul>
    @if ($user->socialAccounts->count() > 0)
        @foreach ($user->socialAccounts as $account)
            <li>
                You have associated <strong>{{ ucfirst($account->provider) }}</strong>
                <time datetime="{{ $account->getDateCreated() }}" title="{{ $account->getDateCreated() }}">
                    {{ $account->getCreatedDateForHumans() }}
                </time>.
                <button onclick="alert('Feature is not yet coded.');">Revoke</button>
            </li>
        @endforeach;
    @else
        <li>Your account is not associated with a third party app.</li>
    @endif
    </ul>

@endsection

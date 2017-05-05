@extends('app')

@section('body-class', 'profiles login tmt-login')

@section('content')
    <a href="{{ action('ProfileController@auth') }}">Log in using another method</a>

    <h1>Create a TMT account</h1>

    @include('partials.forms.profile', ['profile' => auth()->user()->getProfile()])

@endsection

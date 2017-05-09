@extends('app')

@section('body-class', 'profiles settings')

@section('content')

    <h1>Edit your TMT account</h1>
    @include('partials.forms.profile', ['profile' => auth()->user()->getProfile()])


@endsection

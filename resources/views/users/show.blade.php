@extends('app')
@section('body-class', 'profiles show')

@section('content')
<section class="header">
    @include('partials.user-badge', ["user" => $user])
</section>
@endsection

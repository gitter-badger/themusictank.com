@extends('layouts.app')

@section('body-class', 'profiles settings thirdparty')

@section('content')

    @include('partials.user-nav')

    <h1>Settings</h1>
    <h2>API access</h2>

    <p>Having an account on TMT allows you to interact with the website data through a public API. Documentation on how to use it can
     be found in <a href="http://api.themusictank.com/" target="_blank">the documentation pages</a>.</p>

    <p>Your super secret access token is: </p>
    <p>
        plan : free (1,000 requests / day)<br>
        <code>123-abc</code> <button>Generate a new token</button></p>
    </p>

    <h3>Access log</h3>
    <p>You will find bellow the most recent queries to the API made using your access token.<p>
    <p>
        <span><em>0</em> / 1,000</span> requests remaining<br>
        <ul>
            <li>No queries found.</li>
        </ul>
    </p>
@endsection

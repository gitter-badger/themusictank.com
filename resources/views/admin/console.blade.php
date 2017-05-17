@extends('layouts.app')

@section('body-class', 'admin console')

@section('content')

    <h1>Console</h1>

    @include('partials.application-messages')


    <h3>Summary</h3>
    <table>
        <tr>
            <td><em>{{ $artistCount }}</em>artists</td>
            <td><em>{{ $albumCount }}</em>albums</td>
            <td><em>{{ $trackCount }}</em>tracks</td>
        </tr>
    </table>

    <h3>Reset review Cache</h3>

    {{ Form::open(['route' => 'admin-resetcache']) }}
        <fieldset>
            <label for="track_id">Track ID</label>
            <input id="track_id" type="number" name="track_id" required>
        </fieldset>
        <fieldset>
            <label for="user_id">User ID</label>
            <input id="user_id" type="number" name="user_id">
            Keep field empty to reset the global curve instead.
        </fieldset>
        <fieldset>
            <button type="submit">Refresh</button>
        </fieldset>
    </form>

    {{-- <h3>API Requests</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>Model</th>
            <th>Property</th>
            <th>Method</th>
            <th>Timestamp</th>
        </tr>
        @foreach ($apiRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>
                    @if (!is_null($request->UserController))
                        <a href="{{ action('UserController@show', ['id' => $request->profile->slug]) }}">{{ $request->profile->name }}</a>
                    @else
                        Anonymous
                    @endif
                </td>
                <td>{{ $request->model }}</td>
                <td>{{ $request->property }}</td>
                <td>{{ $request->method }}</td>
                <td>{{ $request->getCreatedDateForHumans() }}</td>
            </tr>
        @endforeach
    </table> --}}

@endsection

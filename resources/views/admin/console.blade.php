@extends('app')

@section('body-class', 'admin console')

@section('content')
    <h1>Console</h1>

    <h3>Summary</h3>
    <table>
        <tr>
            <td><em>{{ $artistCount }}</em>artists</td>
            <td><em>{{ $albumCount }}</em>albums</td>
            <td><em>{{ $trackCount }}</em>tracks</td>
        </tr>
    </table>

    <h3>API Requests</h3>
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
                    @if (!is_null($request->profile))
                        <a href="{{ action('ProfileController@show', ['id' => $request->profile->slug]) }}">{{ $request->profile->name }}</a>
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
    </table>

@endsection

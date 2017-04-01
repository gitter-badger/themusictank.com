@extends('app')

@section('body-class', 'tmt home')

@section('content')
    <article>
        <h2>Incentive to try the website<h2>

        <p class="lead">
            Featuring <strong>{{ $artistCount }} artists</strong>,
            totaling an overall discography pool of <strong>{{ $albumCount }} albums</strong>
            and <strong>{{ $trackCount }} tracks</strong>.
        </p>
    </article>
@endsection
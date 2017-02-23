@extends('app')

@section('body-class', 'tmt home')

@section('content')
    <article>
        <h2>Incentive to try the website<h2>

        <p class="lead">
            Featuring <strong>{{ $nbArtists }} artists</strong>,
            totaling an overall discography pool of <strong>{{ $nbAlbums }} albums</strong>
            and <strong>{{ $nbTracks }} tracks</strong>.
        </p>
    </article>
@endsection

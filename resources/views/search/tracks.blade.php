<h3>Tracks</h3>
<ul>
    @if (count($tracks))    
        @foreach ($tracks as $track)
            <li>
                <a href="{{ action('TrackController@show', ['slug' => $track->slug]) }}">
                    {{ $track->name }}
                </a>
                 by 
                 <a href="{{ action('ArtistController@show', ['slug' => $track->artist->slug]) }}">
                    {{ $track->artist->name }}
                </a>
            </li>
        @endforeach
    @else
        <li>Could not find matching tracks.</li>
    @endif
</ul>
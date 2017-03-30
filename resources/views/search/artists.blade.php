<h3>Artists</h3>
<ul>
    @if (count($artists))
        @foreach ($artists as $artist)
            <li><a href="{{ action('ArtistController@show', ['slug' => $artist->slug]) }}">
                {{ $artist->name }}
            </a></li>
        @endforeach
    @else
        <li>Could not find matching artists.</li>
    @endif
</ul>

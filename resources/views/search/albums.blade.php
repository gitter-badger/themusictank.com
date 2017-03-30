<h3>Albums</h3>
<ul>
    @if (count($albums))
        @foreach ($albums as $album)
            <li>
                <a href="{{ action('AlbumController@show', ['slug' => $album->slug]) }}">
                    {{ $album->name }}
                </a>
                 by
                 <a href="{{ action('ArtistController@show', ['slug' => $album->artist->slug]) }}">
                    {{ $album->artist->name }}
                </a>
            </li>
        @endforeach
    @else
        <li>Could not find matching albums.</li>
    @endif
</ul>

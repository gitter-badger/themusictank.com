@if (isset($track))
    <player song-slug="{{ $track->slug }}" song-video="{{ isset($track->youtube_key) ? $track->youtube_key : "" }}" song-length="{{ $track->length }}"></player>
    @include('partials.buttons.bugreport', ['identity' => "song-player", 'location' => Request::url(), 'label' => "Wrong song or something is wrong?"])
@endif

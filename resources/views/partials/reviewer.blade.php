@if (isset($track) && !is_null(auth()->user()))
    <player
        is-review="true"
        autoplay="true"
        song-slug="{{ $track->slug }}"
        song-video="{{ isset($track->youtube_key) ? $track->youtube_key : "" }}"
        album-name="{{ $track->album->name}}"
        song-name="{{ $track->name}}"
        profile-slug="{{ auth()->user()->getProfile()->slug }}"
    >
    <em>this is my additional content!</em>
    </player>
    @include('partials.buttons.bugreport', ['identity' => "song-reviewer", 'location' => Request::url(), 'label' => "Wrong song or something is wrong?"])
@endif

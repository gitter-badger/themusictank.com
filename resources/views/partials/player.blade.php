@if (isset($track))
    <div data-attr="tmt-player" data-song-slug="{{ $track->slug }}" data-song-vid="{{ isset($track->youtube_key) ? $track->youtube_key : "" }}">

        @if (!isset($controls) || (bool)$controls !== false)
            <div class="progress-wrap">
                <div class="progress">
                    <div class="progress-bar loaded-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="{{ $track->length }}" style="width: 0%;"></div>
                    <div class="progress-bar playing-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="{{ $track->length }}" style="width: 0%;"></div>
                </div>
                <div class="cursor"></div>
            </div>

            <i class="play fa fa-stop"></i>
        @endif

        <div class="times">
            <span class="position"> -:--</span> / <span class="duration"> -:--</span>
        </div>
    </div>
@else
    <p>We can't load the player.</p>
@endif

<a href="{{ action("AjaxController@bugreport") }}" class="report-bug" data-bug-iden="song-player" data-bug-location="{{ Request::url() }}">
    <i class="fa fa-bug"></i> Wrong or badly formatted song?
</a>

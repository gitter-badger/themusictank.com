@if (isset($track))
    <section data-attr="tmt-reviewer">

        <span class="knob-track"><i></i><b></b></span>
        @include('partials.player', ['track' => $track])
    </section>
@else
    <p>We couldn't load the reviewer.</p>
@endif

@if (isset($track))

    <section data-attr="tmt-reviewer">
        <i class="knob-track"><b></b></i>
        @include('partials.player', ['track' => $track])
    </section>
@else
    <p>We couldn't load the reviewer.</p>
@endif

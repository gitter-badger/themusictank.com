@if (isset($track))
    <section data-attr="tmt-reviewer">
        <canvas></canvas>
        <span class="knob-track"><b></b></span>
        @include('partials.player', ['track' => $track])
    </section>
@else
    <p>We couldn't load the reviewer.</p>
@endif

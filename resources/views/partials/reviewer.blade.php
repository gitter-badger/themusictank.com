@if (isset($track))
    <section data-attr="tmt-reviewer">
        <canvas></canvas>
        <span class="knob-track"><b></b></span>
        @include('partials.player', ['track' => $track])

        <div class="next-step-mask">
            <div class="next-step">
                <h3>Review complete!</h3>

                <div class="saving">
                    <p>Just a sec, we are still saving your review...</p>
                </div>

                <div class="next">
                    <p>Don't stop, review <i></i> the next track on {{ $track->album->name }}.</p>
                </div>

                <div class="done">
                    <p>Thanks for sharing your opinion.</p>
                </div>

                <div class="sharing">
                    Share your review on social networks
                    @php
                        $text = sprintf("Check out my review of %s!", $track->name);
                        $url = sprintf("/tankers/%s/review/%s/", auth()->user()->getProfile()->slug, $track->slug);
                    @endphp

                    <a class="twitter-share-button" href="#" onclick="window.open('https://twitter.com/intent/tweet?text={{ rawurlencode($text) }}&amp;url={{ $url }}&amp;via=themusictank,'twitter-share-dialog','width=626,height=436'); return false;">Share on Twitter</a>
                    <a class="facebook-share-button" href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ $url }}','facebook-share-dialog','width=626,height=436');return false;">Share on Facebook</a>
                </div>
            </div>
        </div>

    </section>
@else
    <p>We couldn't load the reviewer.</p>
@endif

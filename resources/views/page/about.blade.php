@extends('app')

@section('body-class', 'tmt about')

@section('footer')
<script src="https://maps.googleapis.com/maps/api/js?callback=initMap&amp;key=AIzaSyCuGYmdhpv-ZNoFLAQFfKUywIwJeZNr7vQ" async defer></script>
<script>
      function initMap() {
        var map = new google.maps.Map(document.getElementById('mapCanvas'), {
          center: {lat: 45.4516675, lng: -73.5904749},
          zoom: 8,
            disableDefaultUI: true,
            draggable: false,
            disableDoubleClickZoom: false,
            scrollwheel : false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles : [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#193341"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#2c5a71"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#29768a"},{"lightness":-37}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#3e606f"},{"weight":2},{"gamma":0.84}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"weight":0.6},{"color":"#1a3541"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#2c5a71"}]}]
        });
      }
</script>
@endsection

@section('content')
<div class="header-wrapper map">
    <i id="mapCanvas"></i>
    <i class="mask"></i>
    <article>
        * Based in <a href="https://goo.gl/maps/7NQ1r" target="_blank" rel="noopener noreferrer">Montreal</a>
    </article>
</div>

<article>
    <section class="introduction">
        <h1>About Us</h1>
        <p>We want to give you an easy and fun way to rate tracks and albums organically and accurately. By integrating with various music streaming service, we aspire to collect opinions on the music you listen to.</p>
    </section>

    <section class="team">
        <h2>Meet the team</h2>

        <p class="lead">First launched on the 30th of December 1999, The Music Tank has always been around under a form or another.</p>
        <p class="lead">We are a team of music enthusiasts who work together to build a place where people come hang out and discuss music.</p>

        <div class="row">
            <div class="col-md-3">
                @if(isset($francois))
                    @include('partials.user-badge', ["user" => $francois])
                @endif
            </div>
            <div class="col-md-9">

                <h4>Welcome!</h4>

                <p>I have started TMT way back in 1999, mainly because I wanted a
                sandbox in which I could play and learn how to build websites. How have things changed since then,
                whether in our personal lives, in the music industry or on the Internet itself.</p>

                <p>TMT has had many lives along the way, but there is one moment that I remember particularly fondly.
                <a href="http://web.archive.org/web/20030128044725/http://www.themusictank.com/" target="_blank" rel="noopener noreferrer">In 2003</a>,
                the website had become a place where people would hang out and became less news centric. We didn't have
                any pretension of being able to compete in quality and quantity of content against the myriad of other music
                websites and we just began to have fun. We were a community - albeit a small one - and we'd spend whole days
                hanging out on the message boards.</p>

                <p>Hopefully you are one of the few who might remember this era. If not no worries: you are now part of this
                bright new one.</p>

                <p>The idea behind this iteration of TMT first came to me while I was painstakingly writing a review for
                a band I did not particularly enjoy. English not being my first language, I felt like what I was writing
                was bland and imprecise. The whole process was not a particularly enjoyable one.</p>

                <p>I thought of making a reviewing tool that would automatically and accurately measure what I thought of
                a release without having to write it down.</p>

                <p>Web-based technology has evolved and so has my developer skill set. I have finaly built the tool I wanted, but
                in the end, your pooled opinions have much more value than mine alone. This is why I've built a social website
                around the reviewing tool.</p>

                <p>Hopefully you will enjoy the time you spend here. It means a lot to me to see you.</p>

                <p>Thanks for coming!</p>

                <p><a href="http://www.twitter.com/francoisfaubert">~Francois Faubert</a></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <p>I have not sent my message to Frank yet.</p>
                <p><a href="http://www.twitter.com/julienguay">~Julien Guay</a></p>
            </div>
            <div class="col-md-3">
                @if(isset($francois))
                    @include('partials.user-badge', ["user" => $julien])
                @endif
            </div>
        </div>
    </section>
</article>

@endsection

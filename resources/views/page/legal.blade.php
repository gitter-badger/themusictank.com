@extends('layouts.app')

@section('body-class', 'tmt legal')

@section('content')
<article class="container">
    <div class="row">
        <div class="col-md-12 introduction">
            <h1>Legalities</h1>
            <p class="lead">We are not a business and we do not impose or abide by any obscure legal guidelines. We don't act like douchebags and in turn ask for you to do the same.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Technicalities</h2>
            <p class="lead">We do have some quirks due to the method we obtain some pieces of data but it's nothing you should freak out about.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <ul>
                <li>No music files are hosted, maintained or modified by The Music Tank. The music is
                provided by public Youtube APIs. To prevent us from doing so you may <a href="https://support.google.com/youtube/answer/2807622?hl=en" target="_blank">
                submit a copyright takedown notice</a> directly on Youtube.</li>

                <li>Each time you login to this website using an account linked to an external website (like Facebook), we pull what we think is the bare minimum of information we need to associate it with a TMT account.</li>

                <li>We cannot exactly say what these APIs do with your personal data but one can find information on the provider's homepage.</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul>
                <li>We do not attempt to go snooping around your social networks and we only keep your account id in order for us to re-link your TMT account when you log back in.</li>
                <li>User profiles on TMT are public and everyone can see your ratings.</li>
                <li>When you delete your profile we ask you to keep previous reviews you have done. If you allow us, we keep the reviews though they will no longer point to a resolvable user ID.</li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p style="font-size:8em; text-align:center;"><i class="fa fa-umbrella"></i></p>
            <p style="font-size:.8em; text-align:center;">Phew!</p>
        </div>
    </div>
</article>
@endsection

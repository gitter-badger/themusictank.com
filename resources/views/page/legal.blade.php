@extends('app')

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
            <p class="lead">We do have some quirks, but it's nothing you should freak out about.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <ul>
                <li>All music is not hosted directly on this server. Music is
                provided by public Youtube APIs.</li>

                <li>We have no idea what these APIs do with your personal data, you are going to have to check their agreements for
                yourselves if you care.</li>

                <li>Each time you login to this website using an account linked to an external website (Facebook, Google, etc.), we pull what we think is the bare minimum information we need to remember this link.</li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul>
                <li>We do go snooping around your social networks and we only keep your account id in order for us to re-link your TMT account when you log back in.</li>

                <li>User profiles on TMT are public and everyone can see your ratings.</li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p style="font-size:8em; text-align:center;"><i class="fa fa-umbrella"></i></p>
        </div>
    </div>
</article>
@endsection

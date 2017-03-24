<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" value="origin">
    <meta name="author" content="@francoisfaubert, the active members of the community and contributors on Github as well as all the people who have been around for nearly a decade.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:url" content="http://themusictank.com/">
    <meta property="og:image" content="http://static.themusictank.com/assets/images/social-share.png">
    <meta property="og:site_name" content="The Music Tank">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_CA">
    <meta property="og:title" content="{{ isset($title) ? $title . ' - ' : null }}The Music Tank">
    <meta property="og:description" content="{{ isset($description) ? $description : 'The Music Tank is a place where you can rate and discover music.' }}" >

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@themusictank">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:image:src" content="http://static.themusictank.com/assets/images/social-share.png">
    <meta name="twitter:title" content="{{ isset($title) ? $title . ' - ' : null }}The Music Tank">
    <meta name="twitter:description" content="{{ isset($description) ? $description : 'The Music Tank is a place where you can rate and discover music.' }}">

    <link href="https://plus.google.com/117543200043480372792" rel="publisher">
    <link rel="apple-touch-icon" href="http://static.themusictank.com/assets/images/social-share.png">
    <link rel="icon" href="http://static.themusictank.com/assets/images/social-share.png">

    @stack('header')

	<title>{{ isset($title) ? $title . ' - ' : null }}The Music Tank</title>
</head>
<body class="@yield('body-class', 'home')">
    <header>
        <h1><a href="/" title="The Music Tank">The Music Tank</a></h1>

        <form action="/search/" method="get">
            <input class="typeahead" type="text" name="q" value="" placeholder="Search across everything" />
            <input type="submit" name="search" />
        </form>

        <nav>
            <ul>
                <li><a href="/artists/">Artists</a></li>
                @include('partials.usermenu', ['user' => auth()->user()])
            </ul>
        </nav>
    </header>

    {{ dump(session("loggedUser")) }}

    @yield('backdrop', '<div class="backdrop plain"><i class="mask"></i></div>')

    @yield('content')

    <footer>
        <nav>
            <ul class="social">
                <li><a href="http://www.facebook.com/themusictank/" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook"></i></a></li>
                <li><a href="http://www.twitter.com/themusictank/" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter"></i></a></li>
                <li><a href="https://plus.google.com/117543200043480372792" target="_blank" rel="noopener noreferrer"><i class="fa fa-google-plus"></i></a></li>
            </ul>

            <ul class="internal">
                <li><a href="{{ action("PageController@about") }}">About</a></li>
                <li><a href="{{ action("PageController@legal") }}">Legal</a></li>
                <li>
                    <a href="{{ action("AjaxController@bugreport") }}" class="report-bug" data-bug-iden="general" data-bug-location="{{ Request::url() }}">
                        <i class="fa fa-bug"></i> Found a bug?
                    </a>
                </li>
            </ul>
        </nav>

        <p class="copyright">
            1999 - {{ date('Y') }} The Music Tank <a href="https://www.gnu.org/licenses/quick-guide-gplv3.html" target="_blank" rel="noopener noreferrer">GPL-3.0</a>
        </p>
    </footer>

    @yield('footer')

	<script src="{{ elixir('assets/js/vendor.js') }}"></script>
	<script src="{{ elixir('assets/js/app.js') }}"></script>
    <script>jQuery(function(){(new tmt.App({})).init();});</script>
</body>
</html>

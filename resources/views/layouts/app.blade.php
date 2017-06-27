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
    <meta property="og:title" content="@yield('og-title', 'The Music Tank')">
    <meta property="og:description" content="@yield('og-description', 'The Music Tank is a place where you can rate and discover music.')" >

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@themusictank">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:image:src" content="http://static.themusictank.com/assets/images/social-share.png">
    <meta name="twitter:title" content="@yield('og-title', 'The Music Tank')">
    <meta name="twitter:description" content="@yield('og-description', 'The Music Tank is a place where you can rate and discover music.')" >

    <link rel="apple-touch-icon" href="http://static.themusictank.com/assets/images/social-share.png">
    <link rel="icon" href="http://static.themusictank.com/assets/images/social-share.png">
    <link rel="publisher" href="https://plus.google.com/117543200043480372792">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @stack('header')

	<title>@yield('title', 'The Music Tank')</title>
</head>
<body class="@yield('body-class', 'home')">
    <section class="app">
        @include('partials.header')
        @yield('content')
        @include('partials.footer')
    </section>
    @stack('footer')
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    @include('partials.footer-scripts')
</body>
</html>

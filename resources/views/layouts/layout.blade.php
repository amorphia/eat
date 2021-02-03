<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- Metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="robots" content="index, follow">
    <meta name="referrer" content="always">
    <meta name="description" content="Eat Columbus">
    <title>Eat Columbus</title>

    <!-- Icons -->
    <link rel="apple-touch-icon" href="/images/favicons/apple-touch-icon.png">
    <link rel="icon" href="/images/favicons/favicon.ico">
    <link rel="icon" type="image/png" href="/images/favicons/favicon-16x16.png" sizes=16x16>
    <link rel="icon" type="image/png" href="/images/favicons/favicon-32x32.png" sizes=32x32>
    <link rel="icon" type="image/png" href="/images/favicons/android-chrome-192x192.png" sizes=192x192>
    <link rel="icon" type="image/png" href="/images/favicons/android-chrome-512x512.png" sizes=512x512>

    <!-- Scripts -->
    <script>
        window.App = window.App || {};
        App.url = "{{ config( 'app.url' ) }}";
    </script>

    @stack('header-scripts')
    <script src="{{ mix('js/app.js') }}" defer></script>

    <style>
        @font-face {
            font-family: "HC";
            src: url("/fonts/hero.ttf") format('truetype');

        }
        @font-face {
        font-family: "HC";
            src: url("/fonts/hero-bold.ttf") format('truetype');
            font-weight: bold;
        }
        @font-face {
            font-family: "HC";
            src: url("/fonts/hero-light.ttf") format('truetype');
            font-weight: 300;
        }
    </style>

    <!-- CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- FONTS -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700|Teko:700,500|Oswald:300?subsets=latin' rel='stylesheet' type='text/css'>

</head>
<body id="top" class="@yield( 'page' )">


<div class="main">

    @yield( 'content' )

</div>

@stack('scripts')

</body>
</html>

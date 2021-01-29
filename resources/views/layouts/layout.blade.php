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
    <link rel="apple-touch-icon" href="/favicons/apple-touch-icon.png">
    <link rel="stylesheet" href="https://i.icomoon.io/public/816144a5fb/Eat/style.css">

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
            src: url('/fonts/H-book.woff') format('woff'), /* Pretty Modern Browsers */
            url("/fonts/H-book.ttf") format('truetype');
        }
        @font-face {
        font-family: "HC";
            src: url('/fonts/H-bold.woff') format('woff'),
            url("/fonts/H-bold.ttf") format('truetype');
            font-weight: bold;
        }
        @font-face {
            font-family: "HC";
            src: url('/fonts/H-light.woff') format('woff'),
            url("/fonts/H-light.ttf") format('truetype');
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

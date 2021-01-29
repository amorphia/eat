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

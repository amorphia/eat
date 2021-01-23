<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ mix( 'css/app.css' ) }}">
        <title>Eat Columbus</title>

    </head>
    <body class="antialiased">
        <div id="app">
            <router-view></router-view>

            <hr>
            <router-link class="nav-link" :to="{ name : 'home' }">Home</router-link>
            <router-link class="nav-link" :to="{ name : 'about' }">About</router-link>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
